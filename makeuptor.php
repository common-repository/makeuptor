<?php
/*
    Plugin Name: Makeuptor
    Author: Psyho Inc
    Author URI: http://makeuptor.com/
    Author Email: support@makeuptor.com
    Version: 1.0.2
    Description: Makeuptor is a service that helps decorate your website. With Makeuptor you can add theme decorations, social network icons or various labels and tags in just two clicks. You don’t need to be a programmer to make your homepage look cool! No more boring brain-racking tasks! We want you to have fun decorating your website and we tried to make this process as simple as possible.

    == Copyright ==
    Copyright 2011-2012 Psyho Inc
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
*/



register_activation_hook( __FILE__, array('makeuptor','activate') );
register_deactivation_hook( __FILE__, array('makeuptor','deactivate') );

add_action('admin_menu', array('makeuptor','admin_menu'));
add_filter('wp_footer',array('makeuptor','show_makeuptor'));
add_action('admin_init',array('makeuptor','save_options'));
add_action('plugin_action_links_' . plugin_basename(__FILE__), array('makeuptor','plugin_actions'));

class makeuptor
{
    public function activate()
    {
        $options = array(
            'mu_id'=>'',
        );

        add_option("makeuptor_options", $options, 'Makeuptor Options', 'yes');

    }

    function makeuptor_warning() {
        echo "
            <div id='makeuptor-warning' class='updated fade'><p><strong>".__('Makeuptor is almost ready.')."</strong> ".sprintf(__('Please <a href="%1$s">enter your Makeuptor ID</a>.'), "options-general.php?page=makeuptor")."</p></div>
            ";
    }

    public function deactivate()
    {
        delete_option("makeuptor_options");
    }
    
    public function admin_menu()
    {
        add_options_page('Makeuptor', 'Makeuptor', 'manage_options', 'makeuptor', array('makeuptor','options'));
    }

    function plugin_actions($links) {
        $new_links = array();
    
        $new_links[] = '<a href="options-general.php?page=makeuptor">' . __('Settings') . '</a>';
    
        return array_merge($new_links, $links);
    }

    public function options()
    {
        $option = get_option('makeuptor_options');
        
        $html = '';
        
        $html.='<div class="wrap">';
	$html.='<div id="icon-options-general" class="icon32"><br /></div><h2>Makeuptor Options</h2><br>';
	$html.='
            <form name="optionsForm" method="post" onsubmit="return confirm(\'Do you really want to update these options?\')">
            <input type="hidden" name="makeuptor_options_flag" value="set">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="mu_id">Makeuptor ID</label></th>
                    <td>
                        <input type="text" name="mu_id" id="mu_id" value="'.$option['mu_id'].'" class="small-text"> ' . (($option['mu_id'] == 0)? '<i>(You can <a href="http://makeuptor.com/sites/" target="_blank">find Makeuptor ID</a> in your profile on makeuptor.com)</i>' : '' ) . '
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Plugin?</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Enable Plugin?</span></legend>
                            <p>
                                <label><input type="radio" '.(isset($option['is_enabled']) && $option['is_enabled']?'checked':'').' name="is_enabled" value="1"> Yes</label> 
                                <label><input type="radio" '.(isset($option['is_enabled']) && !$option['is_enabled']?'checked':'').' name="is_enabled" value="0"> No</label>
                            </p>
                        </fieldset>
                    </td>
                </tr>' . (($option['mu_id'] != 0 && $option['is_enabled'])? '
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <p>Excellent! Now you can <a href="' . get_bloginfo ( 'wpurl' ) . '/#makeuptor-edit">decorate your website</a>. Enjoy!</p>
                    </td>
                </tr>' : '' ) . '
            </table>
            
            <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Update Options" /></p>

            </form>
        ';
        
        $html.='</div>';
        
        echo $html;
    }
    
    public function save_options()
    {
        if (isset($_POST['makeuptor_options_flag']))
        {
            $option = get_option('makeuptor_options');
            
            if (isset($_POST['mu_id']))
            {
                $option['mu_id'] = stripcslashes(strip_tags($_POST['mu_id']));
            }
            
            if (isset($_POST['is_enabled']))
            {
                $option['is_enabled'] = intval($_POST['is_enabled'])?true:false;
            }
            
            update_option('makeuptor_options',$option);
            
            header('location: options-general.php?page=makeuptor');
            die();
        }
    }
    
    public function show_makeuptor()
    {
        $option = get_option('makeuptor_options');
        
        if ($option['is_enabled'] && $option['mu_id'] != '')
        {
            ?>
<script type="text/javascript">
    var mu_id=<?php echo $option['mu_id']?>;
</script>
<script type="text/javascript" src="http://static.makeuptor.com/js/mu.js"></script>
            <?php
        }
      
    }
}

if ( is_admin() ){
    $option = get_option('makeuptor_options');

    if ((isset($option['is_enabled']) && $option['is_enabled'] == true && $option['mu_id'] == '') || 
        (!isset($option['is_enabled']) && $option['mu_id'] == '')) {

        add_action('admin_notices', array('makeuptor','makeuptor_warning'));

    }
}

?>