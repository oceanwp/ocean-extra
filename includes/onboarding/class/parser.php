<?php
/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class Ocean_WXR_Parser {
	public function parse( $file ) {
		// Attempt to use proper XML parsers first
		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new Ocean_WXR_Parser_SimpleXML();
			$result = $parser->parse( $file );

			// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {
			$parser = new Ocean_WXR_Parser_XML();
			$result = $parser->parse( $file );

			// If XMLParser succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		}

		// We have a malformed XML file, so display the error and fallthrough to regex
		if ( isset( $result ) && defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
			echo '<pre>';
			if ( 'SimpleXML_parse_error' == $result->get_error_code() ) {
				foreach ( $result->get_error_data() as $error ) {
					echo $error->line . ':' . $error->column . ' ' . esc_html( $error->message ) . "\n";
				}
			} elseif ( 'XML_parse_error' == $result->get_error_code() ) {
				$error = $result->get_error_data();
				echo $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
			}
			echo '</pre>';
			echo '<p><strong>' . __( 'There was an error when reading this WXR file', 'wordpress-importer' ) . '</strong><br />';
			echo __( 'Details are shown above. The importer will now try again with a different parser...', 'wordpress-importer' ) . '</p>';
		}

		// use regular expressions if nothing else available or this is bad XML
		$parser = new Ocean_WXR_Parser_Regex();
		return $parser->parse( $file );
	}
}

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class Ocean_WXR_Parser_SimpleXML {
	public function parse( $file ) {
		$authors    = array();
		$posts      = array();
		$categories = array();
		$tags       = array();
		$terms      = array();

		$internal_errors = libxml_use_internal_errors( true );

		$dom       = new DOMDocument();
		$old_value = null;
		if ( function_exists( 'libxml_disable_entity_loader' ) && PHP_VERSION_ID < 80000 ) {
			$old_value = libxml_disable_entity_loader( true );
		}
		$success = $dom->loadXML( file_get_contents( $file ) );
		if ( ! is_null( $old_value ) ) {
			libxml_disable_entity_loader( $old_value );
		}

		if ( ! $success || isset( $dom->doctype ) ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
		}

		$xml = simplexml_import_dom( $dom );
		unset( $dom );

		// halt if loading produces an error
		if ( ! $xml ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this WXR file', 'wordpress-importer' ), libxml_get_errors() );
		}

		$wxr_version = $xml->xpath( '/rss/channel/wp:wxr_version' );
		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		$wxr_version = (string) trim( $wxr_version[0] );
		// confirm that we are dealing with the correct file format
		if ( ! preg_match( '/^\d+\.\d+$/', $wxr_version ) ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		$base_url = $xml->xpath( '/rss/channel/wp:base_site_url' );
		$base_url = (string) trim( isset( $base_url[0] ) ? $base_url[0] : '' );

		$base_blog_url = $xml->xpath( '/rss/channel/wp:base_blog_url' );
		if ( $base_blog_url ) {
			$base_blog_url = (string) trim( $base_blog_url[0] );
		} else {
			$base_blog_url = $base_url;
		}

		$namespaces = $xml->getDocNamespaces();
		if ( ! isset( $namespaces['wp'] ) ) {
			$namespaces['wp'] = 'http://wordpress.org/export/1.1/';
		}
		if ( ! isset( $namespaces['excerpt'] ) ) {
			$namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';
		}

		// grab authors
		foreach ( $xml->xpath( '/rss/channel/wp:author' ) as $author_arr ) {
			$a                 = $author_arr->children( $namespaces['wp'] );
			$login             = (string) $a->author_login;
			$authors[ $login ] = array(
				'author_id'           => (int) $a->author_id,
				'author_login'        => $login,
				'author_email'        => (string) $a->author_email,
				'author_display_name' => (string) $a->author_display_name,
				'author_first_name'   => (string) $a->author_first_name,
				'author_last_name'    => (string) $a->author_last_name,
			);
		}

		// grab cats, tags and terms
		foreach ( $xml->xpath( '/rss/channel/wp:category' ) as $term_arr ) {
			$t        = $term_arr->children( $namespaces['wp'] );
			$category = array(
				'term_id'              => (int) $t->term_id,
				'category_nicename'    => (string) $t->category_nicename,
				'category_parent'      => (string) $t->category_parent,
				'cat_name'             => (string) $t->cat_name,
				'category_description' => (string) $t->category_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$category['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$categories[] = $category;
		}

		foreach ( $xml->xpath( '/rss/channel/wp:tag' ) as $term_arr ) {
			$t   = $term_arr->children( $namespaces['wp'] );
			$tag = array(
				'term_id'         => (int) $t->term_id,
				'tag_slug'        => (string) $t->tag_slug,
				'tag_name'        => (string) $t->tag_name,
				'tag_description' => (string) $t->tag_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$tag['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$tags[] = $tag;
		}

		foreach ( $xml->xpath( '/rss/channel/wp:term' ) as $term_arr ) {
			$t    = $term_arr->children( $namespaces['wp'] );
			$term = array(
				'term_id'          => (int) $t->term_id,
				'term_taxonomy'    => (string) $t->term_taxonomy,
				'slug'             => (string) $t->term_slug,
				'term_parent'      => (string) $t->term_parent,
				'term_name'        => (string) $t->term_name,
				'term_description' => (string) $t->term_description,
			);

			foreach ( $t->termmeta as $meta ) {
				$term['termmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			$terms[] = $term;
		}

		// grab posts
		foreach ( $xml->channel->item as $item ) {
			$post = array(
				'post_title' => (string) $item->title,
				'guid'       => (string) $item->guid,
			);

			$dc                  = $item->children( 'http://purl.org/dc/elements/1.1/' );
			$post['post_author'] = (string) $dc->creator;

			$content              = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
			$excerpt              = $item->children( $namespaces['excerpt'] );
			$post['post_content'] = (string) $content->encoded;
			$post['post_excerpt'] = (string) $excerpt->encoded;

			$wp                     = $item->children( $namespaces['wp'] );
			$post['post_id']        = (int) $wp->post_id;
			$post['post_date']      = (string) $wp->post_date;
			$post['post_date_gmt']  = (string) $wp->post_date_gmt;
			$post['comment_status'] = (string) $wp->comment_status;
			$post['ping_status']    = (string) $wp->ping_status;
			$post['post_name']      = (string) $wp->post_name;
			$post['status']         = (string) $wp->status;
			$post['post_parent']    = (int) $wp->post_parent;
			$post['menu_order']     = (int) $wp->menu_order;
			$post['post_type']      = (string) $wp->post_type;
			$post['post_password']  = (string) $wp->post_password;
			$post['is_sticky']      = (int) $wp->is_sticky;

			if ( isset( $wp->attachment_url ) ) {
				$post['attachment_url'] = (string) $wp->attachment_url;
			}

			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ) {
					$post['terms'][] = array(
						'name'   => (string) $c,
						'slug'   => (string) $att['nicename'],
						'domain' => (string) $att['domain'],
					);
				}
			}

			foreach ( $wp->postmeta as $meta ) {
				$post['postmeta'][] = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value,
				);
			}

			foreach ( $wp->comment as $comment ) {
				$meta = array();
				if ( isset( $comment->commentmeta ) ) {
					foreach ( $comment->commentmeta as $m ) {
						$meta[] = array(
							'key'   => (string) $m->meta_key,
							'value' => (string) $m->meta_value,
						);
					}
				}

				$post['comments'][] = array(
					'comment_id'           => (int) $comment->comment_id,
					'comment_author'       => (string) $comment->comment_author,
					'comment_author_email' => (string) $comment->comment_author_email,
					'comment_author_IP'    => (string) $comment->comment_author_IP,
					'comment_author_url'   => (string) $comment->comment_author_url,
					'comment_date'         => (string) $comment->comment_date,
					'comment_date_gmt'     => (string) $comment->comment_date_gmt,
					'comment_content'      => (string) $comment->comment_content,
					'comment_approved'     => (string) $comment->comment_approved,
					'comment_type'         => (string) $comment->comment_type,
					'comment_parent'       => (string) $comment->comment_parent,
					'comment_user_id'      => (int) $comment->comment_user_id,
					'commentmeta'          => $meta,
				);
			}

			$posts[] = $post;
		}

		return array(
			'authors'       => $authors,
			'posts'         => $posts,
			'categories'    => $categories,
			'tags'          => $tags,
			'terms'         => $terms,
			'base_url'      => $base_url,
			'base_blog_url' => $base_blog_url,
			'version'       => $wxr_version,
		);
	}
}

/**
 * WXR Parser that makes use of the XML Parser PHP extension.
 */
class Ocean_WXR_Parser_XML {
	public $wp_tags     = array(
		'wp:post_id',
		'wp:post_date',
		'wp:post_date_gmt',
		'wp:comment_status',
		'wp:ping_status',
		'wp:attachment_url',
		'wp:status',
		'wp:post_name',
		'wp:post_parent',
		'wp:menu_order',
		'wp:post_type',
		'wp:post_password',
		'wp:is_sticky',
		'wp:term_id',
		'wp:category_nicename',
		'wp:category_parent',
		'wp:cat_name',
		'wp:category_description',
		'wp:tag_slug',
		'wp:tag_name',
		'wp:tag_description',
		'wp:term_taxonomy',
		'wp:term_parent',
		'wp:term_name',
		'wp:term_description',
		'wp:author_id',
		'wp:author_login',
		'wp:author_email',
		'wp:author_display_name',
		'wp:author_first_name',
		'wp:author_last_name',
	);
	public $wp_sub_tags = array(
		'wp:comment_id',
		'wp:comment_author',
		'wp:comment_author_email',
		'wp:comment_author_url',
		'wp:comment_author_IP',
		'wp:comment_date',
		'wp:comment_date_gmt',
		'wp:comment_content',
		'wp:comment_approved',
		'wp:comment_type',
		'wp:comment_parent',
		'wp:comment_user_id',
	);

	public $wxr_version;
	public $in_post;
	public $cdata;
	public $data;
	public $sub_data;
	public $in_tag;
	public $in_sub_tag;
	public $authors;
	public $posts;
	public $term;
	public $category;
	public $tag;
	public $base_url;
	public $base_blog_url;

	public function parse( $file ) {
		$this->wxr_version = false;
		$this->in_post     = false;
		$this->cdata       = false;
		$this->data        = false;
		$this->sub_data    = false;
		$this->in_tag      = false;
		$this->in_sub_tag  = false;
		$this->authors     = array();
		$this->posts       = array();
		$this->term        = array();
		$this->category    = array();
		$this->tag         = array();

		$xml = xml_parser_create( 'UTF-8' );
		xml_parser_set_option( $xml, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $xml, XML_OPTION_CASE_FOLDING, 0 );
		xml_set_character_data_handler( $xml, array( $this, 'cdata' ) );
		xml_set_element_handler( $xml, array( $this, 'tag_open' ), array( $this, 'tag_close' ) );

		if ( ! xml_parse( $xml, file_get_contents( $file ), true ) ) {
			$current_line   = xml_get_current_line_number( $xml );
			$current_column = xml_get_current_column_number( $xml );
			$error_code     = xml_get_error_code( $xml );
			$error_string   = xml_error_string( $error_code );
			return new WP_Error( 'XML_parse_error', 'There was an error when reading this WXR file', array( $current_line, $current_column, $error_string ) );
		}
		xml_parser_free( $xml );

		if ( ! preg_match( '/^\d+\.\d+$/', $this->wxr_version ) ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		return array(
			'authors'       => $this->authors,
			'posts'         => $this->posts,
			'categories'    => $this->category,
			'tags'          => $this->tag,
			'terms'         => $this->term,
			'base_url'      => $this->base_url,
			'base_blog_url' => $this->base_blog_url,
			'version'       => $this->wxr_version,
		);
	}

	public function tag_open( $parse, $tag, $attr ) {
		if ( in_array( $tag, $this->wp_tags, true ) ) {
			$this->in_tag = substr( $tag, 3 );
			return;
		}

		if ( in_array( $tag, $this->wp_sub_tags, true ) ) {
			$this->in_sub_tag = substr( $tag, 3 );
			return;
		}

		switch ( $tag ) {
			case 'category':
				if ( isset( $attr['domain'], $attr['nicename'] ) ) {
					if ( false === $this->sub_data ) {
						$this->sub_data = array();
					}

					$this->sub_data['domain'] = $attr['domain'];
					$this->sub_data['slug']   = $attr['nicename'];
				}
				break;
			case 'item':
				$this->in_post = true;
				break;
			case 'title':
				if ( $this->in_post ) {
					$this->in_tag = 'post_title';
				}
				break;
			case 'guid':
				$this->in_tag = 'guid';
				break;
			case 'dc:creator':
				$this->in_tag = 'post_author';
				break;
			case 'content:encoded':
				$this->in_tag = 'post_content';
				break;
			case 'excerpt:encoded':
				$this->in_tag = 'post_excerpt';
				break;

			case 'wp:term_slug':
				$this->in_tag = 'slug';
				break;
			case 'wp:meta_key':
				$this->in_sub_tag = 'key';
				break;
			case 'wp:meta_value':
				$this->in_sub_tag = 'value';
				break;
		}
	}

	public function cdata( $parser, $cdata ) {
		if ( ! trim( $cdata ) ) {
			return;
		}

		if ( false !== $this->in_tag || false !== $this->in_sub_tag ) {
			$this->cdata .= $cdata;
		} else {
			$this->cdata .= trim( $cdata );
		}
	}

	public function tag_close( $parser, $tag ) {
		switch ( $tag ) {
			case 'wp:comment':
				unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
				if ( ! empty( $this->sub_data ) ) {
					$this->data['comments'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:commentmeta':
				$this->sub_data['commentmeta'][] = array(
					'key'   => $this->sub_data['key'],
					'value' => $this->sub_data['value'],
				);
				break;
			case 'category':
				if ( ! empty( $this->sub_data ) ) {
					$this->sub_data['name'] = $this->cdata;
					$this->data['terms'][]  = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:postmeta':
				if ( ! empty( $this->sub_data ) ) {
					$this->data['postmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'item':
				$this->posts[] = $this->data;
				$this->data    = false;
				break;
			case 'wp:category':
			case 'wp:tag':
			case 'wp:term':
				$n = substr( $tag, 3 );
				array_push( $this->$n, $this->data );
				$this->data = false;
				break;
			case 'wp:termmeta':
				if ( ! empty( $this->sub_data ) ) {
					$this->data['termmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:author':
				if ( ! empty( $this->data['author_login'] ) ) {
					$this->authors[ $this->data['author_login'] ] = $this->data;
				}
				$this->data = false;
				break;
			case 'wp:base_site_url':
				$this->base_url = $this->cdata;
				if ( ! isset( $this->base_blog_url ) ) {
					$this->base_blog_url = $this->cdata;
				}
				break;
			case 'wp:base_blog_url':
				$this->base_blog_url = $this->cdata;
				break;
			case 'wp:wxr_version':
				$this->wxr_version = $this->cdata;
				break;

			default:
				if ( $this->in_sub_tag ) {
					if ( false === $this->sub_data ) {
						$this->sub_data = array();
					}

					$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_sub_tag                    = false;
				} elseif ( $this->in_tag ) {
					if ( false === $this->data ) {
						$this->data = array();
					}

					$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_tag                = false;
				}
		}

		$this->cdata = false;
	}
}

/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class Ocean_WXR_Parser_Regex {
	public $authors       = array();
	public $posts         = array();
	public $categories    = array();
	public $tags          = array();
	public $terms         = array();
	public $base_url      = '';
	public $base_blog_url = '';
	public $has_gzip;

	public function __construct() {
		$this->has_gzip = is_callable( 'gzopen' );
	}

	public function parse( $file ) {
		$wxr_version  = false;
		$in_multiline = false;

		$multiline_content = '';

		$multiline_tags = array(
			'item'        => array( 'posts', array( $this, 'process_post' ) ),
			'wp:category' => array( 'categories', array( $this, 'process_category' ) ),
			'wp:tag'      => array( 'tags', array( $this, 'process_tag' ) ),
			'wp:term'     => array( 'terms', array( $this, 'process_term' ) ),
		);

		$fp = $this->fopen( $file, 'r' );
		if ( $fp ) {
			while ( ! $this->feof( $fp ) ) {
				$is_tag_line = false;
				$importline  = rtrim( $this->fgets( $fp ) );

				if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
					$wxr_version = $version[1];
				}

				if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
					preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
					$this->base_url = $url[1];
					continue;
				}

				if ( false !== strpos( $importline, '<wp:base_blog_url>' ) ) {
					preg_match( '|<wp:base_blog_url>(.*?)</wp:base_blog_url>|is', $importline, $blog_url );
					$this->base_blog_url = $blog_url[1];
					continue;
				} elseif ( empty( $this->base_blog_url ) ) {
					$this->base_blog_url = $this->base_url;
				}

				if ( false !== strpos( $importline, '<wp:author>' ) ) {
					preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
					$a                                   = $this->process_author( $author[1] );
					$this->authors[ $a['author_login'] ] = $a;
					continue;
				}

				foreach ( $multiline_tags as $tag => $handler ) {
					// Handle multi-line tags on a singular line
					$pos         = strpos( $importline, "<$tag>" );
					$pos_closing = strpos( $importline, "</$tag>" );
					if ( preg_match( '|<' . $tag . '>(.*?)</' . $tag . '>|is', $importline, $matches ) ) {
						$this->{$handler[0]}[] = call_user_func( $handler[1], $matches[1] );

					} elseif ( false !== $pos ) {
						// Take note of any content after the opening tag
						$multiline_content = trim( substr( $importline, $pos + strlen( $tag ) + 2 ) );

						// We don't want to have this line added to `$is_multiline` below.
						$in_multiline = $tag;
						$is_tag_line  = true;

					} elseif ( false !== $pos_closing ) {
						$in_multiline       = false;
						$multiline_content .= trim( substr( $importline, 0, $pos_closing ) );

						$this->{$handler[0]}[] = call_user_func( $handler[1], $multiline_content );
					}
				}

				if ( $in_multiline && ! $is_tag_line ) {
					$multiline_content .= $importline . "\n";
				}
			}

			$this->fclose( $fp );
		}

		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		return array(
			'authors'       => $this->authors,
			'posts'         => $this->posts,
			'categories'    => $this->categories,
			'tags'          => $this->tags,
			'terms'         => $this->terms,
			'base_url'      => $this->base_url,
			'base_blog_url' => $this->base_blog_url,
			'version'       => $wxr_version,
		);
	}

	public function get_tag( $text, $tag ) {
		preg_match( "|<$tag.*?>(.*?)</$tag>|is", $text, $return );
		if ( isset( $return[1] ) ) {
			if ( substr( $return[1], 0, 9 ) == '<![CDATA[' ) {
				if ( strpos( $return[1], ']]]]><![CDATA[>' ) !== false ) {
					preg_match_all( '|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches );
					$return = '';
					foreach ( $matches[1] as $match ) {
						$return .= $match;
					}
				} else {
					$return = preg_replace( '|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1] );
				}
			} else {
				$return = $return[1];
			}
		} else {
			$return = '';
		}
		return $return;
	}

	public function process_category( $c ) {
		$term = array(
			'term_id'              => $this->get_tag( $c, 'wp:term_id' ),
			'cat_name'             => $this->get_tag( $c, 'wp:cat_name' ),
			'category_nicename'    => $this->get_tag( $c, 'wp:category_nicename' ),
			'category_parent'      => $this->get_tag( $c, 'wp:category_parent' ),
			'category_description' => $this->get_tag( $c, 'wp:category_description' ),
		);

		$term_meta = $this->process_meta( $c, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	public function process_tag( $t ) {
		$term = array(
			'term_id'         => $this->get_tag( $t, 'wp:term_id' ),
			'tag_name'        => $this->get_tag( $t, 'wp:tag_name' ),
			'tag_slug'        => $this->get_tag( $t, 'wp:tag_slug' ),
			'tag_description' => $this->get_tag( $t, 'wp:tag_description' ),
		);

		$term_meta = $this->process_meta( $t, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	public function process_term( $t ) {
		$term = array(
			'term_id'          => $this->get_tag( $t, 'wp:term_id' ),
			'term_taxonomy'    => $this->get_tag( $t, 'wp:term_taxonomy' ),
			'slug'             => $this->get_tag( $t, 'wp:term_slug' ),
			'term_parent'      => $this->get_tag( $t, 'wp:term_parent' ),
			'term_name'        => $this->get_tag( $t, 'wp:term_name' ),
			'term_description' => $this->get_tag( $t, 'wp:term_description' ),
		);

		$term_meta = $this->process_meta( $t, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	public function process_meta( $text, $tag ) {
		$parsed_meta = array();

		preg_match_all( "|<$tag>(.+?)</$tag>|is", $text, $meta );

		if ( ! isset( $meta[1] ) ) {
			return $parsed_meta;
		}

		foreach ( $meta[1] as $m ) {
			$parsed_meta[] = array(
				'key'   => $this->get_tag( $m, 'wp:meta_key' ),
				'value' => $this->get_tag( $m, 'wp:meta_value' ),
			);
		}

		return $parsed_meta;
	}

	public function process_author( $a ) {
		return array(
			'author_id'           => $this->get_tag( $a, 'wp:author_id' ),
			'author_login'        => $this->get_tag( $a, 'wp:author_login' ),
			'author_email'        => $this->get_tag( $a, 'wp:author_email' ),
			'author_display_name' => $this->get_tag( $a, 'wp:author_display_name' ),
			'author_first_name'   => $this->get_tag( $a, 'wp:author_first_name' ),
			'author_last_name'    => $this->get_tag( $a, 'wp:author_last_name' ),
		);
	}

	public function process_post( $post ) {
		$post_id        = $this->get_tag( $post, 'wp:post_id' );
		$post_title     = $this->get_tag( $post, 'title' );
		$post_date      = $this->get_tag( $post, 'wp:post_date' );
		$post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
		$comment_status = $this->get_tag( $post, 'wp:comment_status' );
		$ping_status    = $this->get_tag( $post, 'wp:ping_status' );
		$status         = $this->get_tag( $post, 'wp:status' );
		$post_name      = $this->get_tag( $post, 'wp:post_name' );
		$post_parent    = $this->get_tag( $post, 'wp:post_parent' );
		$menu_order     = $this->get_tag( $post, 'wp:menu_order' );
		$post_type      = $this->get_tag( $post, 'wp:post_type' );
		$post_password  = $this->get_tag( $post, 'wp:post_password' );
		$is_sticky      = $this->get_tag( $post, 'wp:is_sticky' );
		$guid           = $this->get_tag( $post, 'guid' );
		$post_author    = $this->get_tag( $post, 'dc:creator' );

		$post_excerpt = $this->get_tag( $post, 'excerpt:encoded' );
		$post_excerpt = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_excerpt );
		$post_excerpt = str_replace( '<br>', '<br />', $post_excerpt );
		$post_excerpt = str_replace( '<hr>', '<hr />', $post_excerpt );

		$post_content = $this->get_tag( $post, 'content:encoded' );
		$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_content );
		$post_content = str_replace( '<br>', '<br />', $post_content );
		$post_content = str_replace( '<hr>', '<hr />', $post_content );

		$postdata = compact(
			'post_id',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_excerpt',
			'post_title',
			'status',
			'post_name',
			'comment_status',
			'ping_status',
			'guid',
			'post_parent',
			'menu_order',
			'post_type',
			'post_password',
			'is_sticky'
		);

		$attachment_url = $this->get_tag( $post, 'wp:attachment_url' );
		if ( $attachment_url ) {
			$postdata['attachment_url'] = $attachment_url;
		}

		preg_match_all( '|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER );
		foreach ( $terms as $t ) {
			$post_terms[] = array(
				'slug'   => $t[2],
				'domain' => $t[1],
				'name'   => str_replace( array( '<![CDATA[', ']]>' ), '', $t[3] ),
			);
		}
		if ( ! empty( $post_terms ) ) {
			$postdata['terms'] = $post_terms;
		}

		preg_match_all( '|<wp:comment>(.+?)</wp:comment>|is', $post, $comments );
		$comments = $comments[1];
		if ( $comments ) {
			foreach ( $comments as $comment ) {
				$post_comments[] = array(
					'comment_id'           => $this->get_tag( $comment, 'wp:comment_id' ),
					'comment_author'       => $this->get_tag( $comment, 'wp:comment_author' ),
					'comment_author_email' => $this->get_tag( $comment, 'wp:comment_author_email' ),
					'comment_author_IP'    => $this->get_tag( $comment, 'wp:comment_author_IP' ),
					'comment_author_url'   => $this->get_tag( $comment, 'wp:comment_author_url' ),
					'comment_date'         => $this->get_tag( $comment, 'wp:comment_date' ),
					'comment_date_gmt'     => $this->get_tag( $comment, 'wp:comment_date_gmt' ),
					'comment_content'      => $this->get_tag( $comment, 'wp:comment_content' ),
					'comment_approved'     => $this->get_tag( $comment, 'wp:comment_approved' ),
					'comment_type'         => $this->get_tag( $comment, 'wp:comment_type' ),
					'comment_parent'       => $this->get_tag( $comment, 'wp:comment_parent' ),
					'comment_user_id'      => $this->get_tag( $comment, 'wp:comment_user_id' ),
					'commentmeta'          => $this->process_meta( $comment, 'wp:commentmeta' ),
				);
			}
		}
		if ( ! empty( $post_comments ) ) {
			$postdata['comments'] = $post_comments;
		}

		$post_meta = $this->process_meta( $post, 'wp:postmeta' );
		if ( ! empty( $post_meta ) ) {
			$postdata['postmeta'] = $post_meta;
		}

		return $postdata;
	}

	public function _normalize_tag( $matches ) {
		return '<' . strtolower( $matches[1] );
	}

	public function fopen( $filename, $mode = 'r' ) {
		if ( $this->has_gzip ) {
			return gzopen( $filename, $mode );
		}
		return fopen( $filename, $mode );
	}

	public function feof( $fp ) {
		if ( $this->has_gzip ) {
			return gzeof( $fp );
		}
		return feof( $fp );
	}

	public function fgets( $fp, $len = 8192 ) {
		if ( $this->has_gzip ) {
			return gzgets( $fp, $len );
		}
		return fgets( $fp, $len );
	}

	public function fclose( $fp ) {
		if ( $this->has_gzip ) {
			return gzclose( $fp );
		}
		return fclose( $fp );
	}
}
