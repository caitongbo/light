<?php
/**
 * This template is used to display the profile editor with [edd_profile_editor]
 */
global $current_user;

if ( is_user_logged_in() ):
    $user_id      = get_current_user_id();
    $first_name   = get_user_meta( $user_id, 'first_name', true );
    $last_name    = get_user_meta( $user_id, 'last_name', true );
    $display_name = $current_user->display_name;
    $address      = edd_get_customer_address( $user_id );
    $states       = edd_get_shop_states( $address['country'] );
    $state 		  = $address['state'];

    if ( edd_is_cart_saved() ): ?>
        <?php $restore_url = add_query_arg( array( 'edd_action' => 'restore_cart', 'edd_cart_token' => edd_get_cart_token() ), edd_get_checkout_uri() ); ?>
        <div class="edd_success edd-alert edd-alert-success"><strong><?php _e( 'Saved cart','easy-digital-downloads' ); ?>:</strong> <?php printf( __( 'You have a saved cart, <a href="%s">click here</a> to restore it.', 'easy-digital-downloads' ), esc_url( $restore_url ) ); ?></div>
    <?php endif; ?>

    <?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == true && ! edd_get_errors() ): ?>
    <div class="edd_success edd-alert edd-alert-success">
        <strong><?php _e( '成功','easy-digital-downloads' ); ?>:</strong>
        <?php _e( '您的信息已经成功更新', 'easy-digital-downloads' ); ?>
    </div>
<?php endif; ?>

    <?php edd_print_errors(); ?>

    <?php do_action( 'edd_profile_editor_before' ); ?>

    <form id="edd_profile_editor_form" class="edd_form" action="<?php echo edd_get_current_page_url(); ?>" method="post">

        <?php do_action( 'edd_profile_editor_fields_top' ); ?>

        <fieldset id="edd_profile_personal_fieldset">

            <legend id="edd_profile_name_label">修改姓名</legend>

            <p id="edd_profile_first_name_wrap">
                <input style="width: 49%" name="edd_first_name" id="edd_first_name" class="text edd-input" type="text" value="<?php echo esc_attr( $first_name ); ?>"  placeholder="姓氏"/>
                <input style="width: 49%" name="edd_last_name" id="edd_last_name" class="text edd-input" type="text" value="<?php echo esc_attr( $last_name ); ?>" placeholder="名"/>
            </p>

            <p id="edd_profile_display_name_wrap">
                <label for="edd_display_name"><?php _e( 'Display Name', 'easy-digital-downloads' ); ?></label>
                <select name="edd_display_name" id="edd_display_name" class="select edd-select">
                    <?php if ( ! empty( $current_user->first_name ) ): ?>
                        <option <?php selected( $display_name, $current_user->first_name ); ?> value="<?php echo esc_attr( $current_user->first_name ); ?>"><?php echo esc_html( $current_user->first_name ); ?></option>
                    <?php endif; ?>
                    <option <?php selected( $display_name, $current_user->user_nicename ); ?> value="<?php echo esc_attr( $current_user->user_nicename ); ?>"><?php echo esc_html( $current_user->user_nicename ); ?></option>
                    <?php if ( ! empty( $current_user->last_name ) ): ?>
                        <option <?php selected( $display_name, $current_user->last_name ); ?> value="<?php echo esc_attr( $current_user->last_name ); ?>"><?php echo esc_html( $current_user->last_name ); ?></option>
                    <?php endif; ?>
                    <?php if ( ! empty( $current_user->first_name ) && ! empty( $current_user->last_name ) ): ?>
                        <option <?php selected( $display_name, $current_user->first_name . ' ' . $current_user->last_name ); ?> value="<?php echo esc_attr( $current_user->first_name . ' ' . $current_user->last_name ); ?>"><?php echo esc_html( $current_user->first_name . ' ' . $current_user->last_name ); ?></option>
                        <option <?php selected( $display_name, $current_user->last_name . ' ' . $current_user->first_name ); ?> value="<?php echo esc_attr( $current_user->last_name . ' ' . $current_user->first_name ); ?>"><?php echo esc_html( $current_user->last_name . ' ' . $current_user->first_name ); ?></option>
                    <?php endif; ?>
                </select>
                <?php do_action( 'edd_profile_editor_name' ); ?>
            </p>

            <?php do_action( 'edd_profile_editor_after_name' ); ?>

            <p id="edd_profile_primary_email_wrap">
                <?php $customer = new EDD_Customer( $user_id, true ); ?>
                <?php if ( $customer->id > 0 ) : ?>

                    <?php if ( 1 === count( $customer->emails ) ) : ?>
                        <legend id="edd_profile_name_label">邮箱地址</legend>

                        <input name="edd_email" id="edd_email" class="text edd-input required" type="email" value="<?php echo esc_attr( $customer->email ); ?>"
                               placeholder="邮箱地址" />
                    <?php else: ?>
                        <?php
                        $emails           = array();
                        $customer->emails = array_reverse( $customer->emails, true );

                        foreach ( $customer->emails as $email ) {
                            $emails[ $email ] = $email;
                        }

                        $email_select_args = array(
                            'options'          => $emails,
                            'name'             => 'edd_email',
                            'id'               => 'edd_email',
                            'selected'         => $customer->email,
                            'show_option_none' => false,
                            'show_option_all'  => false,
                        );

                        echo EDD()->html->select( $email_select_args );
                        ?>
                    <?php endif; ?>
                <?php else: ?>
                    <legend id="edd_profile_name_label">邮箱地址</legend>
                    <input name="edd_email" id="edd_email" class="text edd-input required" type="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" />
                <?php endif; ?>

                <?php do_action( 'edd_profile_editor_email' ); ?>
            </p>

            <?php if ( $customer->id > 0 && count( $customer->emails ) > 1 ) : ?>
                <p id="edd_profile_emails_wrap">
                    <label for="edd_emails"><?php _e( 'Additional Email Addresses', 'easy-digital-downloads' ); ?></label>
                <ul class="edd-profile-emails">
                    <?php foreach ( $customer->emails as $email ) : ?>
                        <?php if ( $email === $customer->email ) { continue; } ?>
                        <li class="edd-profile-email">
                            <?php echo $email; ?>
                            <span class="actions">
								<?php
                                $remove_url = wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'email'      => rawurlencode( $email ),
                                            'edd_action' => 'profile-remove-email',
                                            'redirect'   => esc_url( edd_get_current_page_url() ),
                                        )
                                    ),
                                    'edd-remove-customer-email'
                                );
                                ?>
                                <a href="<?php echo $remove_url ?>" class="delete"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
							</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                </p>
            <?php endif; ?>

            <?php do_action( 'edd_profile_editor_after_email' ); ?>

        </fieldset>


        <fieldset id="edd_profile_password_fieldset" style="margin-top: 5rem">

            <legend id="edd_profile_password_label"><?php _e( '修改您的密码', 'easy-digital-downloads' ); ?></legend>

            <p id="edd_profile_password_wrap">
                <input name="edd_new_user_pass1" id="edd_new_user_pass1" class="password edd-input" type="password" placeholder="输入密码" />
            </p>

            <p id="edd_profile_confirm_password_wrap">
                <input name="edd_new_user_pass2" id="edd_new_user_pass2" class="password edd-input" type="password" placeholder="重复输入密码" />
                <?php do_action( 'edd_profile_editor_password' ); ?>
            </p>

            <?php do_action( 'edd_profile_editor_after_password' ); ?>

        </fieldset>

        <?php do_action( 'edd_profile_editor_after_password_fields' ); ?>

        <fieldset id="edd_profile_submit_fieldset" style="margin-top: 5rem" >

            <p id="edd_profile_submit_wrap" class="pt-20">
                <input type="hidden" name="edd_profile_editor_nonce" value="<?php echo wp_create_nonce( 'edd-profile-editor-nonce' ); ?>"/>
                <input type="hidden" name="edd_action" value="edit_user_profile" />
                <input type="hidden" name="edd_redirect" value="<?php echo esc_url( edd_get_current_page_url() ); ?>" />
                <input name="edd_profile_editor_submit" id="edd_profile_editor_submit" type="submit" class="uk-button uk-button-primary"
                       value="<?php _e( '保存更改', 'easy-digital-downloads' ); ?>"/>
            </p>

        </fieldset>

        <?php do_action( 'edd_profile_editor_fields_bottom' ); ?>

    </form><!-- #edd_profile_editor_form -->

    <?php do_action( 'edd_profile_editor_after' ); ?>

<?php
else:
    do_action( 'edd_profile_editor_logged_out' );
endif;