<?php namespace NpmWeb\FormBuilder\Renderers;

class FoundationBasicGridRenderer implements RendererInterface {

    public function processOptions( $config ) {
        // no changes
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
            <div class="<?php echo $config->columns_class ?> columns">
                <label for="<?php echo $config->extras['id'] ?>" <?php if($error) { echo 'class="error"'; } ?>><?php echo $config->label; ?>
                <?php if ( isset($config->prefix) ) { ?>
                    <div class="row collapse <?php if($error) { echo 'error'; } ?>">
                        <div class="<?php echo $config->prefix['columns_class'] ?> columns">
                            <span class="prefix"><?php echo $config->prefix['label']; ?></span>
                        </div>
                        <div class="<?php echo $config->main['columns_class'] ?> columns">
                <?php } // end if prefix ?>
                <?php echo $control /* pre-escaped */ ?>
                <?php if($error): ?>
                    <small class="error"><?php echo $error ? esc_body($error) : '' ?></small>
                <?php endif ?>
                <?php if ( isset($config->prefix) ) { ?>
                        </div>
                    </div>
                <?php } ?>
                </label>
            </div>
        <?php if( $rowPerField ): ?></div><?php endif ?>
        <?php
        return ob_get_clean();
    }

    public function renderCheckboxOrRadio( $fieldname, $config, $error, $control ) {
        $rowPerField = false;
        if( property_exists($config,'row_per_field') ) {
            $rowPerField = $config->row_per_field;
        }
        ob_start();
        ?>
        <?php if( $rowPerField ): ?><div class="row"><?php endif ?>
            <div class="<?php echo $config->columns_class ?> columns">
                <?php echo $control /* pre-escaped */ ?>
                <label for="<?php echo $fieldname ?>"><?php echo $config->label ?></label>
            </div>
        <?php if( $rowPerField ): ?></div><?php endif ?>
        <?php
        return ob_get_clean();
    }

}
