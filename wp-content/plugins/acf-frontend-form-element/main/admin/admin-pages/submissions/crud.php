<?php

namespace Frontend_WP\Admin;


if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !class_exists( 'Submissions_Crud' ) ) {
    class Submissions_Crud
    {
        public function create_submissions()
        {
            global  $wpdb ;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'fea_submissions';
            $sql = "CREATE TABLE {$table_name} (\r\n\t\t\t\tid mediumint(9) NOT NULL AUTO_INCREMENT,\r\n\t\t\t\ttitle text NOT NULL,\r\n\t\t\t\tcreated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,\r\n\t\t\t\tuser int NOT NULL,\r\n\t\t\t\tfields longtext NOT NULL,\r\n\t\t\t\tform text NOT NULL,\r\n\t\t\t\tstatus text NOT NULL,\r\n\t\t\t\tUNIQUE KEY id (id)\r\n\t\t\t) {$charset_collate};";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            maybe_create_table( $table_name, $sql );
        }
        
        public function insert_submission( $args )
        {
            global  $wpdb ;
            if ( empty($args['title']) ) {
                $args['title'] = '';
            }
            $wpdb->insert( $wpdb->prefix . 'fea_submissions', $args );
            $submits_count = get_option( 'frontend_admin_submissions_all_time', 0 );
            $submits_count++;
            update_option( 'frontend_admin_submissions_all_time', $submits_count );
            return $wpdb->insert_id;
        }
        
        public function update_submission( $id, $args )
        {
            if ( isset( $args['fields'] ) && is_array( $args['fields'] ) ) {
                $args['fields'] = acf_encrypt( json_encode( $args['fields'] ) );
            }
            global  $wpdb ;
            $wpdb->update( $wpdb->prefix . 'fea_submissions', $args, array(
                'id' => $id,
            ) );
        }
        
        public function approve_submission( $id )
        {
            global  $wpdb ;
            $submission = $this->get_submission( $id );
            
            if ( $submission->status == 'pending' ) {
                $form = $this->get_submission_form( $submission );
                foreach ( fea_instance()->local_actions as $action ) {
                    $name = $action->get_name();
                    $form = $action->run( $form );
                }
                $wpdb->update( $wpdb->prefix . 'fea_submissions', array(
                    'status' => 'approved',
                ), array(
                    'id' => $id,
                ) );
            }
        
        }
        
        public function get_submission( $id = 0 )
        {
            if ( !$id ) {
                return $id;
            }
            global  $wpdb ;
            $id = (int) $id;
            $submission = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}fea_submissions WHERE id = %d", $id ) );
            return $submission;
        }
        
        public function get_status_label( $slug )
        {
            switch ( $slug ) {
                case 'in_progress':
                    return __( 'In Progress', FEA_NS );
                case 'require_approval':
                    return __( 'Pending Approval', FEA_NS );
                case 'verify_email':
                    return __( 'Pending Email Verification', FEA_NS );
                case 'email_verified':
                    return __( 'Email Verified', FEA_NS );
                case 'pending_payment':
                    return __( 'Pending Payment', FEA_NS );
                case 'approved':
                    return __( 'Approved', FEA_NS );
                default:
                    return $slug;
            }
        }
        
        /**
         * Retrieve submissions data from the database
         *
         * @param array $args query arguments
         *
         * @return mixed
         */
        public static function get_submissions( $args = array() )
        {
            global  $wpdb ;
            if ( !empty($_REQUEST) ) {
                $args = array_merge( $args, $_REQUEST );
            }
            $args = feadmin_parse_args( $args, array(
                'per_page'     => 20,
                'current_page' => 1,
            ) );
            $sql = "SELECT * FROM {$wpdb->prefix}fea_submissions";
            
            if ( isset( $args['form'] ) ) {
                $form = true;
                $sql .= $wpdb->prepare( " WHERE form = %s", $args['form'] );
            }
            
            
            if ( isset( $args['form_key'] ) ) {
                $form = true;
                if ( isset( $form ) ) {
                    $sql .= " OR ";
                }
                $sql .= $wpdb->prepare( " WHERE form = %s", $args['form_key'] );
            }
            
            
            if ( isset( $args['form_id'] ) ) {
                if ( isset( $form ) ) {
                    $sql .= " OR ";
                }
                $sql .= $wpdb->prepare( " WHERE form = %d", $args['form_id'] );
            }
            
            
            if ( !empty($_REQUEST['s']) ) {
                $value = $_REQUEST['s'] . '%';
                $sql .= $wpdb->prepare( ' WHERE title LIKE %s', $value );
            }
            
            
            if ( !empty($_REQUEST['orderby']) ) {
                $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
                $sql .= ( !empty($_REQUEST['order']) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC' );
            } else {
                $sql .= ' ORDER BY ' . sanitize_sql_orderby( 'created_at DESC' );
            }
            
            
            if ( $args['per_page'] != '-1' ) {
                $sql .= $wpdb->prepare( " LIMIT %d", $args['per_page'] );
                $sql .= $wpdb->prepare( " OFFSET %d", ($args['current_page'] - 1) * (int) $args['per_page'] );
            }
            
            $result = $wpdb->get_results( $sql, 'ARRAY_A' );
            return $result;
        }
        
        /**
         * Returns the count of records in the database.
         *
         * @return null|string
         */
        public static function record_count( $args = array() )
        {
            global  $wpdb ;
            $sql = "SELECT COUNT(form) FROM {$wpdb->prefix}fea_submissions";
            
            if ( isset( $args['form'] ) ) {
                $form = true;
                $sql .= $wpdb->prepare( " WHERE form = %s", $args['form'] );
            }
            
            
            if ( isset( $args['form_key'] ) ) {
                $form = true;
                if ( isset( $form ) ) {
                    $sql .= " OR ";
                }
                $sql .= $wpdb->prepare( " WHERE form = %s", $args['form_key'] );
            }
            
            
            if ( isset( $args['form_id'] ) ) {
                if ( isset( $form ) ) {
                    $sql .= " OR ";
                }
                $sql .= $wpdb->prepare( " WHERE form = %d", $args['form_id'] );
            }
            
            return $wpdb->get_var( $sql );
        }
        
        public function delete_submission( $id = 0 )
        {
            if ( $id == 0 ) {
                return $id;
            }
            global  $wpdb ;
            $wpdb->delete( $wpdb->prefix . 'fea_submissions', array(
                'id' => $id,
            ) );
            return 1;
        }
        
        public function display_submissions()
        {
            require_once __DIR__ . '/list.php';
            $display_list = true;
            
            if ( isset( $_REQUEST['id'] ) ) {
                $submission_id = $_REQUEST['id'];
                $submission = $this->get_submission( $submission_id );
                
                if ( $submission ) {
                    $user = get_user_by( 'ID', $submission->user );
                    
                    if ( is_object( $user ) ) {
                        $title = $user->display_name . ' (' . $user->user_login . ')';
                    } else {
                        $title = '--';
                    }
                    
                    ?>
					<h2><?php 
                    echo  sprintf( 'Submission #%d: %s', $submission_id, $title ) ;
                    ?></h2>
					<?php 
                    $action = fea_instance()->submissions_list->current_action();
                    
                    if ( $action == 'edit' ) {
                        $form = json_decode( acf_decrypt( $submission->fields ), true );
                        
                        if ( isset( $form['record'] ) ) {
                            $approval_form = $this->get_submission_form( $submission, $form, true );
                            fea_instance()->form_display->render_form( $approval_form );
                            
                            if ( !empty($form['record']['emails_to_verify']) ) {
                                echo  '<div class="emails-to-verify">' ;
                                echo  '<h3>' . __( 'Pending Verification:', FEA_NS ) . '</h3><ul>' ;
                                foreach ( $form['record']['emails_to_verify'] as $address ) {
                                    echo  '<li>' . $address . '</li>' ;
                                }
                                echo  '</ul></div>' ;
                            }
                            
                            
                            if ( !empty($form['record']['verified_emails']) ) {
                                echo  '<div class="verified-emails">' ;
                                echo  '<h3>' . __( 'Verified Emails:', FEA_NS ) . '</h3><ul>' ;
                                foreach ( $form['record']['verified_emails'] as $address ) {
                                    echo  '<li>' . $address . '</li>' ;
                                }
                                echo  '</ul></div>' ;
                            }
                        
                        }
                        
                        $display_list = false;
                    } elseif ( $action == 'delete' ) {
                        if ( empty($_REQUEST['nonce']) ) {
                            die( 'Nonce required to perform this action.' );
                        }
                        $nonce = esc_attr( $_REQUEST['nonce'] );
                        if ( !wp_verify_nonce( $nonce, 'frontend_admin_delete_submission' ) ) {
                            die( 'Go get a life script kiddies' );
                        }
                        $this->delete_submission( $submission_id );
                    }
                
                } else {
                    $display_list = true;
                }
            
            }
            
            
            if ( $display_list ) {
                fea_instance()->submissions_list->prepare_items();
                ?>
				<h1 class="wp-heading-inline"><?php 
                echo  __( 'Submissions', FEA_NS ) ;
                ?></h1>
				<form method="post">
				<?php 
                fea_instance()->submissions_list->search_box( 'search', 'search_id' );
                fea_instance()->submissions_list->display();
                ?> </form> <?php 
            }
        
        }
        
        public function get_submission_form( $submission, $form = array(), $approval = false )
        {
            if ( is_string( $submission ) ) {
                $submission = $this->get_submission( $submission );
            }
            if ( empty($form) ) {
                $form = json_decode( acf_decrypt( $submission->fields ), true );
            }
            $fields = array();
            if ( !empty($form['record']['fields']) ) {
                foreach ( $form['record']['fields'] as $type ) {
                    if ( is_array( $type ) ) {
                        $fields = array_merge( $fields, $type );
                    }
                }
            }
            //$form['record']['fields'] = $fields;
            $approval_form = array_merge( $form, array(
                'fields' => $fields,
            ) );
            
            if ( $approval ) {
                
                if ( $submission->status == 'approved' ) {
                    $submit_value = __( 'Update', FEA_NS );
                    $success_message = __( 'Data has been updated.', FEA_NS );
                    $update = true;
                } else {
                    $submit_value = __( 'Approve', FEA_NS );
                    $success_message = __( 'Submission has been approved. Data has been saved.', FEA_NS );
                }
                
                $approval_form = array_merge( $approval_form, array(
                    'submission'          => $submission->id,
                    'id'                  => $form['id'],
                    'submit_value'        => $submit_value,
                    'kses'                => 0,
                    'no_cookies'          => 1,
                    'approval'            => 1,
                    'submitted_by'        => $submission->user,
                    'submitted_on'        => $submission->created_at,
                    'return'              => admin_url( 'admin.php?page=' . FEA_PRE . '-submissions&action=edit&id=' . $form['id'] ),
                    'ajax_submit'         => 0,
                    'update_message'      => $success_message,
                    'show_update_message' => 1,
                ) );
            }
            
            $data_types = array( 'post', 'user', 'term' );
            foreach ( $data_types as $type ) {
                
                if ( isset( $form['record'][$type] ) ) {
                    $approval_form["{$type}_id"] = $form['record'][$type];
                    
                    if ( isset( $update ) ) {
                        $approval_form["save_to_{$type}"] = 'edit_' . $type;
                    } else {
                        $approval_form["save_to_{$type}"] = $form["save_to_{$type}"];
                    }
                
                }
            
            }
            return $approval_form;
        }
        
        public function submissions_page_options()
        {
            
            if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == FEA_PRE . '-submissions' ) {
                $option = 'per_page';
                $args = [
                    'label'   => 'Submissions',
                    'default' => 20,
                    'option'  => 'submissions_per_page',
                ];
                add_screen_option( $option, $args );
            }
        
        }
        
        function set_submissions_per_page( $status, $option, $value )
        {
            if ( 'submissions_per_page' == $option ) {
                return $value;
            }
            return $status;
        }
        
        public function custom_admin_pages()
        {
            if ( !get_option( 'frontend_admin_save_submissions' ) ) {
                return;
            }
            global  $fea_submissions_page ;
            $fea_submissions_page = add_submenu_page(
                FEA_PRE . '-settings',
                __( 'Submissions', FEA_NS ),
                __( 'Submissions', FEA_NS ),
                'manage_options',
                FEA_PRE . '-submissions',
                [ $this, 'display_submissions' ],
                81
            );
            add_action( "load-{$fea_submissions_page}", array( $this, 'submissions_page_options' ) );
        }
        
        public function transfer_acff_rows_to_new_table()
        {
            global  $wpdb ;
            $old_name = $wpdb->prefix . 'acff_submissions';
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_name ) );
            if ( $wpdb->get_var( $query ) !== $old_name ) {
                return;
            }
            $sql = "SELECT * FROM {$old_name}";
            $results = $wpdb->get_results( $sql, 'ARRAY_A' );
            
            if ( $results ) {
                foreach ( $results as $result ) {
                    $submission = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$old_name} WHERE id = %d", $result['id'] ) );
                    
                    if ( $submission ) {
                        $this->insert_submission( $result );
                        $wpdb->delete( $old_name, array(
                            'id' => $result['id'],
                        ) );
                    }
                
                }
            } else {
                $wpdb->query( "DROP TABLE IF EXISTS {$old_name}" );
            }
            
            $sql = "SELECT * FROM {$old_name}";
            $results = $wpdb->get_results( $sql, 'ARRAY_A' );
            if ( $results ) {
                foreach ( $results as $result ) {
                    $submission = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$old_name} WHERE id = %d", $result['id'] ) );
                    
                    if ( $submission ) {
                        $this->insert_submission( $result );
                        $wpdb->delete( $old_name, array(
                            'id' => $result['id'],
                        ) );
                    }
                
                }
            }
        }
        
        public function transfer_fa_rows_to_new_table()
        {
            global  $wpdb ;
            $old_name = $wpdb->prefix . 'frontend_admin_submissions';
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $old_name ) );
            if ( $wpdb->get_var( $query ) !== $old_name ) {
                return;
            }
            $sql = "SELECT * FROM {$old_name}";
            $results = $wpdb->get_results( $sql, 'ARRAY_A' );
            
            if ( $results ) {
                foreach ( $results as $result ) {
                    $submission = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$old_name} WHERE id = %d", $result['id'] ) );
                    
                    if ( $submission ) {
                        $this->insert_submission( $result );
                        $wpdb->delete( $old_name, array(
                            'id' => $result['id'],
                        ) );
                    }
                
                }
            } else {
                $wpdb->query( "DROP TABLE IF EXISTS {$old_name}" );
            }
        
        }
        
        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'custom_admin_pages' ), 19 );
            $this->create_submissions();
            add_filter(
                'set-screen-option',
                array( $this, 'set_submissions_per_page' ),
                11,
                3
            );
            add_action( 'init', array( $this, 'transfer_acff_rows_to_new_table' ) );
            add_action( 'init', array( $this, 'transfer_fa_rows_to_new_table' ) );
        }
    
    }
    fea_instance()->submissions_handler = new Submissions_Crud();
}
