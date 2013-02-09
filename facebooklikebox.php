<?php

	
if (!defined('_PS_VERSION_'))
	exit;
	
class facebooklikebox extends Module {
	private $configVars = array();
	
	
	
	public function __construct() {
		
		$this->name = 'facebooklikebox';				
		$this->tab = 'front_office_features';		
		$this->version = '1.0';
		$this->author = 'Sergio Carracedo www.sergiocarracedo.es';
		
		
		$this->configVars=array(
			"pageurl" 		=> array(
									'title' 	=> $this->l('Facebook Page URL'),
									'default' 	=> ''
								),
			"width"			=> array(
									'title' 	=> $this->l('Width'),
									'default' 	=> 210
								),
			"height"		=> array(
									'title' 	=> $this->l('Height'),
									'default' 	=> ''
								),
			"colorscheme"	=> array(
									'title' 	=> $this->l('Color Scheme'),
									'default' 	=> 'light',									
									'values' => array('light','dark')
								),
							
			"showfaces"		=> array(
									'title' 	=> $this->l('Show Faces'),
									'default' 	=> 'true',									
									'values' => array('true','false')
								),
			"bordercolor"	=> array(
									'title' 	=> $this->l('Border Color'),
									'default' 	=> ''
								),
			"stream"		=> array(
									'title' 	=> $this->l('Show Stream'),
									'default' 	=> 'true',									
									'values' => array('true','false')
								),
			"header"		=> array(
									'title' 	=> $this->l('Show header'),
									'default' 	=> 'true',									
									'values' => array('true','false')
								)	
		);
		
		
		parent::__construct();

		$this->displayName = $this->l('Facebook Like Box');
		$this->description = $this->l('Add a Facebook Like Box Block');
	}
	
	public function install() {
		$res=parent::install();
		
		foreach ($this->configVars as $varName => $var) {
			$res = $res &&  Configuration::updateValue($this->name."_".$varName,$var["default"]);			
		}			

		$res= $res && $this->registerHook('header') 
			&& $this->registerHook('rightColumn') 
			&& $this->registerHook('leftColumn');
		
		return $res;
		
	}
	
	public function uninstall() {
		$res=true;		
		foreach ($this->configVars as $varName => $var) {
			$res = $res &&  Configuration::deleteByName($this->name."_".$varName);			
		}			
		
		return $res &&  parent::uninstall();
		
	}
	
	public function getContent() {
		$html = '';
		// If we try to update the settings
		if (isset($_POST['submitModule']))  {
			
			foreach ($this->configVars as $varName => $var) {
				$value= ((isset($_POST[$this->name."_".$varName])  ? $_POST[$this->name."_".$varName] : ''));
				
				Configuration::updateValue($this->name."_".$varName,$value);			
			}		
			
			$html .= '<div class="confirm">'.$this->l('Configuration updated').'</div>';
		}

		$html .= '
		<h2>'.$this->displayName.'</h2>
		<form action="'.Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>';
			foreach ($this->configVars as $varName => $var) {
				$fullVarName=$this->name."_".$varName;	
				if (empty($var["values"])) {
					$html.='<p><label for="'.$varName.'">'.$var["title"].' :</label>
					<input type="text" id="'.$fullVarName.'" name="'.$fullVarName.'" value="'.Configuration::get($fullVarName).'" /></p>';
				} else {
					$html.='<p><label for="'.$varName.'">'.$var["title"].' :</label>
					<select id="'.$fullVarName.'" name="'.$fullVarName.'">';
					foreach ($var["values"] as $value) {
						if (Configuration::get($fullVarName)==$value) {
							$html.='<option value="'.$value.'" selected="selected">'.$value.'</option>';
						} else {
							$html.='<option value="'.$value.'">'.$value.'</option>';
						}
					}
					
					$html.='</select>';					
				}
			}	
		
				
				$html.='<div class="margin-form">
					<input type="submit" name="submitModule" value="'.$this->l('Update settings').'" class="button" /></center>
				</div>
			</fieldset>
		</form>
		';
		
		return $html;
	}
	
	public function hookHeader() {
		//$this->context->controller->addCSS(($this->_path).'blockcontactinfos.css', 'all');
	}
	
	public function hookLeftColumn($params) {
		$this->hookRightColumn($params);
	}
	
	public function hookRightColumn($params) {
		$height = Configuration::get('facebooklikebox_height');
		
		if (empty($height)) {
			$height= 590;
		}
		
		return '<div id="facebooklikebox" class="block"><iframe src="//www.facebook.com/plugins/likebox.php?'.
			'href='.urlencode(Configuration::get('facebooklikebox_pageurl')).
			'&amp;width='.Configuration::get('facebooklikebox_width').
			'&amp;height='.$height.
			'&amp;colorscheme='.Configuration::get('facebooklikebox_colorscheme').
			'&amp;show_faces='.Configuration::get('facebooklikebox_showfaces').
			'&amp;border_color='.Configuration::get('facebooklikebox_bordercolor').
			'&amp;stream='.Configuration::get('facebooklikebox_stream').
			'&amp;header='.Configuration::get('facebooklikebox_header').
			'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.
			Configuration::get('facebooklikebox_width').'px; height:'.$height.'px;" allowTransparency="true"></iframe></div>';
	}
}
?>







