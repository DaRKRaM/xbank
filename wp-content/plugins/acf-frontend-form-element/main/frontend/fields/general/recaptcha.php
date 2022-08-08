<?php
/* 
    Credit due to @hwk-fr 
    Plugin Page: https://wordpress.org/plugins/acf-extended/

*/

if(!defined('ABSPATH'))
    exit;

if( ! class_exists( 'acf_frontend_field_recaptcha' ) ) : 
    class acf_frontend_field_recaptcha extends acf_field{
        
        function initialize(){
            
            $this->name = 'recaptcha';
            $this->label = __('Google reCaptcha', FEA_NS);
            $this->category = __( 'Security', FEA_NS );
            $this->defaults = array(
                'required'      => 0,
                'disabled'      => 0,
                'readonly'      => 0,
                'version'       => 'v2',
                'v2_theme'      => 'light',
                'v2_size'       => 'normal',
                'v3_hide_logo'  => false,
                'site_key'      => get_option( 'frontend_admin_google_recaptcha_site' ),
                'secret_key'    => get_option( 'frontend_admin_google_recaptcha_secret' ),
            );
                        
        }

        
        function render_field_settings($field){
            
            // Version
            acf_render_field_setting($field, array(
                'label'			=> __('Version', FEA_NS),
                'instructions'	=> __('Select the reCaptcha version', FEA_NS),
                'type'			=> 'select',
                'name'			=> 'version',
                'choices'		=> array(
                    'v2'    => __('reCaptcha V2', FEA_NS),
                    'v3'    => __('reCaptcha V3', FEA_NS),
                )
            ));
            
            // V2 Theme
            acf_render_field_setting($field, array(
                'label'			=> __('Theme', FEA_NS),
                'instructions'	=> __('Select the reCaptcha theme', FEA_NS),
                'type'			=> 'select',
                'name'			=> 'v2_theme',
                'choices'		=> array(
                    'light' => __('Light', FEA_NS),
                    'dark'  => __('Dark', FEA_NS),
                ),
                'conditional_logic' => array(
                    array(
                        array(
                            'field'     => 'version',
                            'operator'  => '==',
                            'value'     => 'v2',
                        )
                    )
                )
            ));
            
            // V2 Size
            acf_render_field_setting($field, array(
                'label'			=> __('Size', FEA_NS),
                'instructions'	=> __('Select the reCaptcha size', FEA_NS),
                'type'			=> 'select',
                'name'			=> 'v2_size',
                'choices'		=> array(
                    'normal'    => __('Normal', FEA_NS),
                    'compact'   => __('Compact', FEA_NS),
                ),
                'conditional_logic' => array(
                    array(
                        array(
                            'field'     => 'version',
                            'operator'  => '==',
                            'value'     => 'v2',
                        )
                    )
                )
            ));
            
            // V3 Hide Logo
            acf_render_field_setting($field, array(
                'label'			=> __('Hide logo', FEA_NS),
                'instructions'	=> __('Hide the reCaptcha logo', FEA_NS),
                'type'			=> 'true_false',
                'name'			=> 'v3_hide_logo',
                'ui'            => true,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'     => 'version',
                            'operator'  => '==',
                            'value'     => 'v3',
                        )
                    )
                )
            ));
            
            // Site Key
            acf_render_field_setting($field, array(
                'label'			=> __('Site key', FEA_NS),
                'instructions'	=> __('Enter the site key. <a href="https://www.google.com/recaptcha/admin" target="_blank">reCaptcha API Admin</a>', FEA_NS),
                'type'			=> 'text',
                'name'			=> 'site_key',
            ));
            
            // Site Secret
            acf_render_field_setting($field, array(
                'label'			=> __('Secret key', FEA_NS),
                'instructions'	=> __('Enter the secret key. <a href="https://www.google.com/recaptcha/admin" target="_blank">reCaptcha API Admin</a>', FEA_NS),
                'type'			=> 'text',
                'name'			=> 'secret_key',
            ));

        }

        
        function prepare_field($field){
            
            if($field['version'] === 'v3'){
                
                $field['wrapper']['class'] = 'acf-hidden';
                
            }
            
            return $field;
            
        }
        
        function render_field($field){
            // Site key
            $site_key = $field['site_key'] ? $field['site_key'] : get_option('frontend_admin_google_recaptcha_site');
            if( empty( $site_key ) ) return;

            // Version
            $field['version'] = $field['version'] ? $field['version'] : 'v3';

            // V2
            if($field['version'] === 'v2'){ ?>
            
                <?php
                
                $wrapper = array(
                    'class'         => 'acf-input-wrap frontend-admin-recaptcha',
                    'data-site-key' => $site_key,
                    'data-version'  => 'v2',
                    'data-size'     => $field['v2_size'],
                    'data-theme'    => $field['v2_theme'],
                );
                
                $hidden_input = array(
                    'id'    => $field['id'],
                    'name'  => $field['name'],
                );
                
                ?>
                <div <?php acf_esc_attr_e($wrapper); ?>>
                    
                    <div></div>
                    <?php acf_hidden_input($hidden_input); ?>
                    
                </div>
                
                <script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=acffRecaptcha" async defer></script>

                <script type="text/javascript">
                var acffRecaptcha = function() {
                   acf.getField('<?php echo $field['key']; ?>').onLoad();
                };
                </script>

                <?php
                return;

            }
            
            // V3
            elseif($field['version'] === 'v3'){
                
                $wrapper = array(
                    'class'         => 'acf-input-wrap frontend-admin-recaptcha',
                    'data-site-key' => $site_key,
                    'data-version'  => 'v3',
                );
                
                $hidden_input = array(
                    'id'    => $field['id'],
                    'name'  => $field['name'],
                );
                
                ?>
                <div <?php acf_esc_attr_e($wrapper); ?>>
                    
                    <div></div>
                    <?php acf_hidden_input($hidden_input); ?>
                    
                </div>
                
                <?php if($field['v3_hide_logo']){ ?>
                    <style>
                    .grecaptcha-badge{
                        display: none;
                        visibility: hidden;
                    }
                    </style>
                <?php } ?>
                
                <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>" async defer></script>
                
                <?php
                return;
                
            }

        }
        
        function validate_value($valid, $value, $field, $input){
            
            // Expired
            if($value === 'expired'){
                
                return __('reCaptcha has expired.');

            }
            
            // Error
            elseif($value === 'error'){
                
                return __('An error has occured.');

            }
            
            // Only true submission
            elseif(!wp_doing_ajax()){
                
                // Empty & Required
                if(empty($value) && $field['required']){
                    
                    return $valid;
                    
                }
                
                // Success
                else{
                    
                    // Secret key
					$secret_key = $field['secret_key'] ? $field['secret_key'] : get_option('frontend_admin_google_recaptcha_secret');


                    // API Call
                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$value}");
                    curl_setopt($curl, CURLOPT_HEADER, 0);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $api = curl_exec($curl);

                    curl_close($curl);
                    
                    // No response
                    if(empty($api))
                        return false;
                    
                    $response = json_decode($api);
                    
                    if($response->success === false){
                        
                        $valid = false;
                        
                    }
                    
                    elseif($response->success === true){
                        
                        $valid = true;
                        
                    }
                    
                }
                
            }
            
            return $valid;
            
        }
        
        function update_value($value, $post_id, $field){
            
            // Do not save field value
            return null;
            
        }

    }

    // initialize
    acf_register_field_type('acf_frontend_field_recaptcha');
endif;