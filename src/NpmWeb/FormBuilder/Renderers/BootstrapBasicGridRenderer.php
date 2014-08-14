<?php namespace NpmWeb\FormBuilder\Renderers;

class BootstrapBasicGridRenderer implements RendererInterface {

    public function processOptions( $config ) {
        if( array_key_exists('class',$config->extras) ) {
            $config->extras['class'] .= ' form-control';
        } else {
            $config->extras['class'] = 'form-control';
        }
        return $config;
    }

    public function renderFormControl( $fieldname, $config, $error, $control ) {
        $rowPerField = false;
        if( property_exists($config,'row_per_field') ) {
            $rowPerField = $config->row_per_field;
        }
        ob_start();
        ?>
        <?php if( $rowPerField ): ?><div class="row"><?php endif ?>
            <div class="form-group <?php echo $config->columns_class ?><?php if($error) { echo ' has-error'; } ?>">
                <label for="<?php echo $config->extras['id'] ?>"><?php echo $config->label; ?></label>
                <?php if ( isset($config->prefix) ) { ?>
                    <div class="input-group">
                        <div class="input-group-addon">@</div>
                <?php } // end if prefix ?>
                <?php echo $control /* pre-escaped */ ?>
                <?php if($error): ?>
                    <span class="help-block"><?php echo $error ? esc_body($error) : '' ?></small>
                <?php endif ?>
                <?php if ( isset($config->prefix) ) { ?>
                    </div>
                <?php } ?>
            </div>
        <?php if( $rowPerField ): ?></div><?php endif ?>
        <?php
        return ob_get_clean();
    }

}
