<?php

/**
 * Un bloc représente une partie de HTML à "render", soit une vue, soit un Layout
 *
 */
class Block {

	/**
	 * CodeIgniter superobject instance
	 *
	 * @var object
	 * @access protected
	 */
	protected $CI;

	/**
	 * Le nom complet de la vue typique CI
	 * @var string
	 */
	protected $view;

	/**
	 * Le tableaau de données de la vue pour le load->view() de cette vue
	 * @var string
	 */
	protected $data;
	
	/**
	 * Le marqueur indiquant où ce bloc doit être inclus dans la vue du layout père
	 * @var string
	 */
	protected $marker;
	
	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * Indique la vue correspondant au bloc
	 */
	public function setView($view, $marker = null, $data = null) {
		$this->view = $view;
		$this->marker = $marker;
		$this->data = $data;
	}

	public function getView() {
		return $this->view;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getMarker() {
		return $this->marker;
	}
	
	/**
	 * Surchargée par Layout et ViewLayout
	 */
	protected function render(){

	}
	

}

/*
 * Cette classe réprésente non seulement le layout racine chargé par la librairie
 * mais également chacun des layouts enfants
 * Cette classe est donc un Bloc
 * 
 */
class Layout extends Block {	
		
	/**
	 * Liste des blocs du layout, soit une vue soit un autre layout
	 * @var unknown_type
	 */
	protected $blocks = array();
			
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Ajoute une nouvelle vue enfant
	 */
	public function addView($name, $marker, $data = null){
		$newView = new ViewLayout();
		$newView->setView($name, $marker, $data);
		$this->blocks[] = $newView;
	}
	
	/**
	 * Ajoute un nouveau layout enfant
	 */
	public function addLayout($layout){
		$this->blocks[] = $layout;
	}
	
	/**
	 * Lance le render global de toutes les vues et sous-layouts
	 */
	public function renderAll(){
		$contents = array();
		foreach($this->blocks as $block) {
			$contents[$block->getMarker()] = $block->render();
		}
		if($this->data != null){
			$contents = array_merge($contents, $this->data);
		}
		$this->CI->load->view($this->view, $contents, false);
	}
	
	/**
	 * Render tous les blocs du layout
	 */
	protected function render(){
		$contents = array();
		foreach($this->blocks as $block) {
			$contents[$block->getMarker()] = $block->render();
		}
		if($this->data != null){
			$contents = array_merge($contents, $this->data);
		}
		return $this->CI->load->view($this->view, $contents, true);
	}
	
}

/*
 * Cette classe représente une vue incluse dans un Layout
 * 
 */
class ViewLayout extends Block {
	
	/**
	 * Render la vue
	 */
	protected function render(){
		return $this->CI->load->view($this->getView(), $this->getData(), true);
	}
}


/* End of file Layout.php */