<?php namespace NpmWeb\FormBuilder;

use Config;
use Illuminate\Database\Eloquent\Model;
use Log;

class FormBuilder
	extends \NpmWeb\ClientValidationGenerator\Laravel\FormBuilder
{
	
	private $default_col_width = 'large-6';

	public function __construct(
		\Illuminate\Html\HtmlBuilder $html,
		\Illuminate\Routing\UrlGenerator $url,
		$csrfToken)
	{
		parent::__construct($html,$url,$csrfToken);
		$this->default_col_width = Config::get('laravel-forms::col_width');
		$this->default_row_per_field = Config::get('laravel-forms::row_per_field');
	}

	/**
	 * Display a field read-only
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function readonly($fieldname, $default = null, $options = [])
	{
		$label = ( isset($options['label']) ? $options['label'] : $this->label($fieldname) );
		$columns_class = ( isset($options['columns_class'] ) ? $options['columns_class'] : $this->default_col_width );

		// get model
		if( $this->model ) {
			$model = $this->model;
		} else {
			throw new \Exception('no model specified');
		}

		// format data
		$value = null;
		if( !is_null($default) ) {
			$value = $default;
		} elseif( property_exists($model, $fieldname) ) {
			$value = $model->$fieldname;
		} elseif( $model instanceof Model
			&& array_key_exists( $fieldname, $model->getAttributes())
		) {
			$value = $model->$fieldname;
		} elseif( $model instanceof Model
			&& method_exists($model, $fieldname)
		) {
			// relationship
			$value = $model->$fieldname;
		}
		if( array_key_exists('format',$options) ) {
			$format = $options['format'];
			switch($format) {
				case 'url':
					if(isset($options['url'])) {
						$callback = $options['url'];
						$url = $callback($value);
					} else {
						$url = $value;
					}
					$value = '<a href="'.esc_attr($url).'" target="_blank">'.esc_body($value).'</a>';
					$options['escape'] = false;
					break;
				case 'image':
					if(isset($options['url'])) {
						$callback = $options['url'];
						$url = $callback($value);
					} else {
						$url = $value;
					}
					$value = '<img src="'.esc_attr($url).'" />';
					$options['escape'] = false;
					break;
				case 'email': 
					$value = '<a href="mailto:'.esc_attr($value).'">'.esc_body($value).'</a>';
					$options['escape'] = false;
					break;
				case 'checkbox':
					$value = $value ? 'Yes' : 'No';
					break;
				default: // date/time
					if( $value ) {
						$value = $value->format($format);
					}
					break;
			}
		}
		if( !array_key_exists('escape',$options) || true == $options['escape'] ) {
			$value = esc_body($value);
		}
		$value = '<div><strong>'.$value.'</strong></div>';

		$config = $this->_processOptions($fieldname, $options);
		return $this->_outputHelper( $fieldname, $config, $value );
	}

	/**
	 * Create a text input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function text($name, $default = null, $options = [])
	{
		//Log::debug(__METHOD__.'()');

		if( !$default && array_key_exists('format',$options) ) {
			$format = $options['format'];
			$value = $this->model->$name;
			switch($format) {
				case 'url':
					$value = '<a href="'.esc_attr($value).'">'.esc_body($value).'</a>';
					break;
				case 'email': 
					$value = '<a href="mailto:'.esc_attr($value).'">'.esc_body($value).'</a>';
					break;
				default: // date/time
					if( $value ) {
						$value = $value->format($format);
					}
					break;
			}
			$default = $value;
		}

		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, parent::text($name, $default, $config->extras) );
	}

	/**
	 * Create a password input field.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @return string
	 */
	public function password($name, $options = array())
	{
		//Log::debug(__METHOD__.'()');
		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, 
			parent::password($name, $config->extras) );
	}

	/**
	 * Create an e-mail input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function email($name, $value = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');
		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, 
			parent::email($name, $value, $config->extras) );
	}

	/**
	 * Create a telephone input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function tel($name, $value = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');
		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, 
			parent::input('tel', $name, $value, $config->extras) );
	}

	/**
	 * Create a number input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function number($name, $value = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');
		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, 
			parent::input('number', $name, $value, $config->extras) );
	}

	/**
	 * Create a file input field.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function file($name, $value = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');
		$config = $this->_processOptions($name, $options);
		$value = $this->getValueAttribute($name,$value);
		$input = '';
		if( $value ) {
			$value = $this->getValueAttribute($name,$value);
			if( array_key_exists('format',$options) ) {
				switch($options['format']) {
					case 'url': 
						$callback = $options['url'];
						$url = $callback($value);
						$value = '<a href="'.esc_attr($url).'" target="_blank">'.esc_body($value).'</a>';
						break;
					case 'image':
						if(isset($options['url'])) {
							$callback = $options['url'];
							$url = $callback($value);
						} else {
							$url = $value;
						}
						$value = '<img src="'.esc_attr($url).'" />';
						break;
					}
			}
			$input = 'Current: '.$value;
			// $model = $this->model;
			// if already uploaded, not "required" to upload a new one
			// $model::$rules[$name] = array_diff( $model::$rules[$name], ['required']);
			// var_dump($model::$rules);exit;
		}
		$input .= '<br />Upload New: '.parent::file($name, $config->extras);
		return $this->_outputHelper( $name, $config, $input );
	}

	/**
	 * Create a textarea.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function textarea($name, $value = null, $options = array())
	{
		$config = $this->_processOptions($name, $options);
		return $this->_outputHelper( $name, $config, 
			parent::textarea($name, $value, $config->extras) );
	}

	/**
	 * Create a select box field.
	 *
	 * @param  string  $name
	 * @param  array   $list
	 * @param  string  $selected
	 * @param  array   $options
	 * @return string
	 */
	public function select($name, $list = array(), $selected = null, $options = array())
	{
		$config = $this->_processOptions($name,$options);

		return $this->_outputHelper( $name, $config, parent::select($name, $list, $selected, $config->extras) );
	}

	/**
	 * Create a radio button input field.
	 *
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  bool    $checked
	 * @param  array   $options
	 * @return string
	 */
	public function radio($name, $value = null, $checked = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');

		$config = $this->_processOptions($name,$options);
		//$radioHtml = parent::radio($name, $value, $checked, $config->extras);
		$id = (array_key_exists('id', $config->extras) ? $config->extras['id'] : esc_attr($name.'_'.$value) );
		ob_start(); 
		?>
		<div class="<?php echo $config->columns_class ?> columns">
			<label class="radio">
				<input type="radio" name="<?php echo $name ?>" value="<?php echo $value ?>" id="<?php echo $config->extras['id'] ?>"<?php if ($checked) echo ' checked="checked"'; ?>><span class="radio"><?php echo $config->label; ?></span>
			</label>
		</div>				

		<?php
		return ob_get_clean();

	}

	/**
	 * Create a single checkbox input field.
	 *
	 * @see http://nielson.io/2014/02/handling-checkbox-input-in-laravel/
	 * @param  string  $name
	 * @param  mixed   $value
	 * @param  bool    $checked
	 * @param  array   $options
	 * @return string
	 */
	public function checkbox($name, $value = 1, $checked = null, $options = array())
	{
		//Log::debug(__METHOD__.'()');

		$config = $this->_processOptions($name,$options);
		//$radioHtml = parent::radio($name, $value, $checked, $config->extras);
		$id = (array_key_exists('id', $config->extras) ? $config->extras['id'] : esc_attr($name.'_'.$value) );
		ob_start(); 
		?>
		<div class="<?php echo $config->columns_class ?> columns">
			<label class="radio">
				<?php echo parent::hidden( $name, false ) ?>
				<?php echo parent::checkbox( $name, $value, $checked, $options ) ?>
				<span class="radio"><?php echo $config->label; ?></span>
			</label>
		</div>				

		<?php
		return ob_get_clean();

	}


	// go through the options and pull out the pieces we're interested in
	protected function _processOptions( $name, $options )
	{
		$config = new \stdClass();
		$config->label = ( isset($options['label']) ? 
							esc_body($options['label']) : 
							( isset($options['labelHtml']) ? $options['labelHtml'] : $this->formatLabel($name,null) )
						);
		$config->columns_class = ( isset($options['columns_class'] ) ? $options['columns_class'] : $this->default_col_width );
		$config->row_per_field = ( isset($options['row_per_field'] ) ? $options['row_per_field'] : $this->default_row_per_field );
		// \Log::debug('processing options for '.$name);
		$config->extras = array_key_exists('extras',$options)
			? $options['extras']
			: [];

		if (!array_key_exists('id', $config->extras) ) $config->extras['id'] = esc_attr( $name );
		if (!array_key_exists('placeholder',$config->extras) ) $config->extras['placeholder'] = $config->label; // configurable?

		$config->errors = array_key_exists('errors',$options)
			? $options['errors']
			: null;	

		if (array_key_exists('prefix', $options)) $config->prefix = $options['prefix'];
		if (array_key_exists('main', $options)) $config->main = $options['main'];
		if (array_key_exists('postfix', $options)) $config->postfix = $options['postfix'];

		$model = $this->model;
		if( !array_key_exists( 'maxlength', $config->extras )
			&& property_exists($model,'rules')
			&& is_array($model::$rules)
			&& array_key_exists( $name, $model::$rules ) )
		{
			foreach( $model::$rules[$name] as $fieldRule ) {
				if( FALSE !== strpos($fieldRule,'max:') ) {
					$config->extras['maxlength'] = substr($fieldRule,4);
				}
			}
		}

		return $config;

	}
/*

Sample Foundation Text Input
<div class="large-6 columns">
	<label for="contact_first_name">First Name
		<input type="text" name="contact_first_name" id="contact_first_name" maxlength="50" placeholder="First Name" required/>
		<small class="error">First Name is required</small>
	</label>
</div><!--/ large-6 -->

Sample prefixed text input
<div class="medium-6 columns">
	<label for="pmt_amount">Gift Amount (USD)
		<div class="row collapse">
			<div class="small-3 large-2 columns">
				<span class="prefix">$</span>
			</div>
			<div class="small-9 large-10 columns">
				<input type="text" name="pmt_amount" id="pmt_amount" maxlength="14" placeholder="0.00" required>
				<small class="error">Please Specify Amount</small>
			</div><!--/ small-9 -->
		</div><!--/ row -->
	</label>
</div>
*/

	protected function _outputHelper( $fieldname, $config, $control ) {
		//Log::debug(__METHOD__.'('.$fieldname.')');
		$error = null;
		if( property_exists($config,'errors')
			&& $config->errors instanceof \Illuminate\Support\MessageBag
		) {
			$error = $config->errors->first($fieldname);
		}
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
				<div class="<?php echo $config->main['columns_class'] ?> columns"><?php } // end if prefix ?> 
				<?php echo $control /* pre-escaped */ ?>
				<?php if($error): ?>
					<small class="error"><?php echo $error ? esc_body($error) : '' ?></small>
				<?php endif ?>
			<?php if ( isset($config->prefix) ) { ?>
				</div>
			</div><?php } ?>
			</label>
		</div>
		<?php if( $rowPerField ): ?></div><?php endif ?>
		<?php
		return ob_get_clean();
	}

}
