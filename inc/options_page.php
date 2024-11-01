<?php
/**
 * Vwriter Guest Post Options Page
 */
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2>vWriter Guest Post Plugin</h2>
    <p>
        This page allows you to set your options for for the vWriter Guest Post plugin.

    </p>

    <p>

        The plugin allows you to build up links to your blog through the power of guest posting, all taken care of for you semi-automatically.

    </p>

    <p>

        Visitors to your blog can request a unique copy of one of your blog posts for their own blog, via a link that the plugin places at the bottom of all posts (see the <i>Scope</i> setting below), or on individual posts (via a checkbox when you add or edit a post).       

    </p>

    <p>
        
        More details about how the plugin works in conjunction with vWriter.com can be found <a href="https://clients.vwriter.com/guestpostplugin.php" target="_blank">here</a> (login required).
        
    </p>
    
    <p>

        The link placed at the end of your post(s) points to <a href="http://vwriter.com" target="_blank">vWriter.com</a>'s writing service - a client account with vWriter.com is a requirement for the plugin to work properly (it's free to <a href="https://clients.vwriter.com" target="_blank">register</a>).

    </p>
    
    <p>
        
        Use the options below to:
        
    </p>
    
    <ol>
        
        <li>Add the link to all posts - or not</li>
        
        <li>Enter your vWriter.com <a href="https://clients.vwriter.com/guestpostplugin.php" target="_blank">Client ID</a></li>
        
        <li>Define the text that will appear before the link itself - you can add HTML codes as required, eg. <i>&lt;strong&gt;, &lt;i&gt;</i> etc.</li>
        
        <li>Define the anchor text itself</li>
        
    </ol>
    
    <p align="center">
        <img src="/wp-content/plugins/vwriter-guest-post/images/vwriter-plugin.png">
    </p>
    
    <form method="post" action="options.php">

        <?php
        // Setup and retrieve our option values so they're available for use in our form
        settings_fields( 'vwriter_guest_post_options' );
        $options = get_option( 'vwriter_guest_post' );
        ?>

        <table class="form-table" style="margin-top: 20px; padding-bottom: 10px; border: 1px dotted #bbb; border-width:1px 0;">

            <tr valign="top">
                <th scope="row">Scope</th>
                <td>

                    <input id="add_to_all" name="vwriter_guest_post[add_to_all]" class="checkbox" type="checkbox" value="1" <?php
                    if( isset( $options['add_to_all'] ) ){
                        checked( '1', $options['add_to_all'] );
                    }
                    ?> />
                    <label for="add_to_all"> Add to all posts</label>

                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="vwriter_client_id">vWriter.com Client ID (you can find out what your client ID is <a href="https://clients.vwriter.com/guestpostplugin.php" target="_blank">here</a>)</label>
                </th>
                <td>
                    <input id="vwriter_client_id" name="vwriter_guest_post[vwriter_client_id]" type="text" value="<?php echo $options['vwriter_client_id']; ?>" />
                    Not a vWriter.com client? <a href="https://clients.vwriter.com" target="_blank">Register here</a>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="vwriter_client_id">Pre-Text</label>
                </th>
                <td>
                    <textarea class="mce_editor" name="vwriter_guest_post[post_text]" style="width: 90%; height: 150px; padding: 10px;"><?php echo $options['post_text']; ?></textarea>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="vwriter_client_id">Anchor Text</label>
                </th>
                <td>
                    <input id="post_anchor_text" name="vwriter_guest_post[post_anchor_text]" type="text" value="<?php echo $options['post_anchor_text']; ?>" />
                </td>
            </tr>

        </table>

        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
        </p>

    </form>
</div>