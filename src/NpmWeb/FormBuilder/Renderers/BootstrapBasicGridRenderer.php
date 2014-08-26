<?php namespace NpmWeb\FormBuilder\Renderers;

class BootstrapBasicGridRenderer implements RendererInterface {

    public function processOptions( $config, $type  ) {
        if( !in_array( $type, array('radio','checkbox','file') ) ) {
            if( array_key_exists('class',$config->extras) ) {
                $config->extras['class'] .= ' form-control';
            } else {
                $config->extras['class'] = 'form-control';
            }
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
            <div class="form-group <?php echo e($config->columns_class) ?><?php if($error) { echo ' has-error'; } ?>">
                <label for="<?php echo e($config->extras['id']) ?>"><?php echo e($config->label) ?></label>

                <?php if ( isset($config->tooltip) ) { ?>
                    <span style='margin-left: 4px;' class='glyphicon glyphicon-question-sign' data-toggle="tooltip" data-placement="bottom" title="<?php echo e($config->tooltip) ?>"></span>
                <?php } ?>

                <?php if ( isset($config->prefix) ) { ?>
                    <div class="input-group">
                        <div class="input-group-addon">@</div>
                <?php } // end if prefix ?>
                <?php echo $control /* pre-escaped */ ?>

                <?php if ( isset($config->example) ) { ?>
                    <span style="font-size:12px; margin-left:4px; margin-top:4px; display:inline-block;"><?php echo e($config->example) ?></small>
                <?php } // end if prefix ?>

                <?php if($error): ?>
                    <span class="help-block"><?php echo $error ? e($error) : '' ?></small>
                <?php endif ?>
                <?php if ( isset($config->prefix) ) { ?>
                    </div>
                <?php } ?>
            </div>
        <?php if( $rowPerField ): ?></div><?php endif ?>
        <?php
        return ob_get_clean();
    }

    public function renderCheckboxOrRadio( $fieldname, $config, $error, $control ) {
        ob_start();
        ?>
        <div class="<?php echo e($config->columns_class) ?> checkbox">
            <label>
                <?php echo $control /* pre-escaped */ ?>
                <?php echo e($config->label); ?>
            </label>
        </div>

        <?php
        return ob_get_clean();
    }

}
