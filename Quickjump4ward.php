<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Quickjump4ward
 * A Contao-Extension to quickly access cartain backend-modules
 * through typing
 *
 * @copyright  4ward.media 2012 <http://www.4wardmedia.de>
 * @author     Christoph Wiechert <christoph.wiechert@4wardmedia.de>
 * @package    quickjump4ward
 * @license    LGPL
 * @filesource
 */


class Quickjump4ward extends Backend {
       
	protected $ret = array();
	protected $limitEach = 5;

	public function __construct()
	{
		$this->import('BackendUser','User');
		$this->User->authenticate();

		parent::__construct();

		$this->base = $this->Environment->base.((substr($this->Environment->base,-1) != '/') ? '/':'').'contao/';

	}

	public function getResult($s,$type = 'all')
	{
		$s = urldecode($s);

		// tipple the limitEach if weve a specific type
		if($type !='all') $this->limitEach *= 3;

		$autoTypes = $this->User->quickjump4ward;
		if(!is_array($autoTypes)) $autoTypes == array();

		if($type == 'page' || ($type =='all' && in_array('page',$autoTypes)))
			$this->addPages($s);
		if($type == 'article' || ($type =='all' && in_array('article',$autoTypes)))
			$this->addArticles($s);
		if($type == 'module' || ($type == 'all' && in_array('module',$autoTypes)))
			$this->addModules($s);
		if($type == 'pagelayout' || ($type == 'all' && in_array('pagelayout',$autoTypes)))
			$this->addLayouts($s);
		if($type == 'stylesheet' || ($type == 'all' && in_array('stylesheet',$autoTypes)))
			$this->addStylesheets($s);
		if($type == 'function' || ($type == 'all' && in_array('function',$autoTypes)))
			$this->addFunctions($s);
		if($type == 'new' || ($type == 'all' && in_array('new',$autoTypes)))
			$this->addNewElemes($s);

		// HOOK
		if(is_array($GLOBALS['TL_HOOKS']['quickjump4ward']) && count($GLOBALS['TL_HOOKS']['quickjump4ward']) > 0)
		{
			foreach($GLOBALS['TL_HOOKS']['quickjump4ward'] as $callback)
			{
				$this->import($callback[0]);
				$this->ret = array_merge($this->ret,$this->$callback[0]->$callback[1]($s, $type));
			}
		}

		return $this->ret;
	}


	/**
	 * Add modules to ret-array
	 * @param str $s
	 */
	protected function addModules($s)
	{
		if(!$this->User->hasAccess('modules', 'themes')) return;

		$objModule = $this->Database->prepare('SELECT id,name FROM tl_module WHERE name LIKE ? ORDER BY name')
			->limit($this->limitEach)
			->execute('%'.$s.'%');
		while($objModule->next())
		{
			$this->ret[] = array
			(
				'type'  => 'module',
				'name'  => 'm:'.$objModule->name,
				'url'   => $this->base.'main.php?do=themes&table=tl_module&act=edit&id='.$objModule->id,
				'image' => $this->generateImage('modules.gif')
			);
		}
	}


	/**
	 * Add pagelayouts to ret-array
	 * @param str $s
	 */
	protected function addLayouts($s)
	{
		if(!$this->User->hasAccess('layout', 'themes')) return;

		$objLayout = $this->Database->prepare('SELECT id,name FROM tl_layout WHERE name LIKE ? ORDER BY name')
			->limit($this->limitEach)
			->execute('%'.$s.'%');
		while($objLayout->next())
		{
			$this->ret[] = array
			(
				'type'  => 'layout',
				'name'  => 'pl:'.$objLayout->name,
				'url'   => $this->base.'main.php?do=themes&table=tl_layout&act=edit&id='.$objLayout->id,
				'image' => $this->generateImage('layout.gif')
			);
		}
	}


	/**
	 * Add functions to ret-array
	 * @param str $s
	 */
	protected function addFunctions($s)
	{
		$arrFunc = array();
		$arrFunc[] = array
		(
			'type'  => 'function',
			'name'  => 'f:Database update',
			'url'   => $this->base.'main.php?do=repository_manager&update=database',
			'image' => $this->generateImage('system/modules/rep_client/themes/default/images/dbcheck16.png')
		);
		$arrFunc[] = array
		(
			'type'  => 'function',
			'name'  => 'f:Cache löschen',
			'url'   => $this->base.'main.php?do=maintenance&quickjump4ward=doClearCache',
			'image' => $this->generateImage('cache.gif')
		);
		$arrFunc[] = array
		(
			'type'  => 'function',
			'name'  => 'f:Extension installieren',
			'url'   => $this->base.'main.php?do=repository_manager&install=extension',
			'image' => $this->generateImage('system/modules/rep_client/themes/default/images/install16.png')
		);

		foreach($arrFunc as $func)
		{
			if(empty($s) || stripos($func['name'],$s))
			{
				$this->ret[] = $func;
			}
		}
	}


	/**
	 * Add stylesheets to ret-array
	 * @param str $s
	 */
	protected function addStylesheets($s)
	{
		if(!$this->User->hasAccess('css', 'themes')) return;

		if(strrpos($s,':') !== false)
		{
			$data = explode(':',$s);
			// Specific stylesheet adressed, show categories
			$objCategory = $this->Database->prepare('SELECT category,ss.id FROM tl_style AS s
               													LEFT JOIN tl_style_sheet AS ss ON(s.pid = ss.id) 
               													WHERE ss.name = ? AND s.category LIKE ? 
               													GROUP BY s.category ORDER BY s.category')
				->limit($this->limitEach)
				->execute($data[0],'%'.$data[1].'%');
			while($objCategory->next())
			{
				$this->ret[] = array
				(
					'type'  => 'stylesheet',
					'name'  => 'css:'.$data[0].':'.$objCategory->category,
					'url'   => $this->base.'main.php?do=themes&table=tl_style&id='.$objCategory->id.'&category='.urlencode($objCategory->category),
					'image' => $this->generateImage('css.gif')
				);
			}
		}
		else
		{
			//show stylesheets
			$objStylesheets = $this->Database->prepare('SELECT id,name FROM tl_style_sheet WHERE name LIKE ? ORDER BY name')
				->limit($this->limitEach)
				->execute('%'.$s.'%');
			while($objStylesheets->next())
			{
				$this->ret[] = array
				(
					'type'  => 'stylesheet',
					'name'  => 'css:'.$objStylesheets->name,
					'url'   => $this->base.'main.php?do=themes&table=tl_style&id='.$objStylesheets->id,
					'image' => $this->generateImage('css.gif')
				);
			}
		}
	}

	/**
	 * Add new elements creators to ret-array
	 * @param str $s
	 */
	protected function addNewElemes($s)
	{
		$objThemes = $this->Database->execute('SELECT id,name FROM tl_theme ORDER BY name');
		$arrThemes = array();
		while($objThemes->next()) $arrThemes[$objThemes->id] = $objThemes->name;


		if(strrpos($s,':') !== false)
		{
			$data = explode(':',$s);

			if(!strlen($data[1]) || strripos('Module',$data[1]) !== false)
			{
				$this->ret[] = array
				(
					'type'  => 'new',
					'name'  => 'new:'.$data[0].':Module',
					'url'   => $this->base.'main.php?do=themes&table=tl_module&id='.array_search($data[0],$arrThemes).'&act=create&mode=2&pid='.array_search($data[0],$arrThemes),
					'image' => $this->generateImage('modules.gif')
				);
			}
			if(!strlen($data[1]) || strripos('Stylesheet',$data[1]) !== false)
			{
				$this->ret[] = array
				(
					'type'  => 'new',
					'name'  => 'new:'.$data[0].':Stylesheet',
					'url'   => $this->base.'main.php?do=themes&table=tl_style_sheet&id='.array_search($data[0],$arrThemes).'&act=create&mode=2&pid='.array_search($data[0],$arrThemes),
					'image' => $this->generateImage('css.gif')
				);
			}
			if(!strlen($data[1]) || strripos('Pagelayout',$data[1]) !== false)
			{

				$this->ret[] = array
				(
					'type'  => 'new',
					'name'  => 'new:'.$data[0].':Pagelayout',
					'url'   => $this->base.'main.php?do=themes&table=tl_layout&id='.array_search($data[0],$arrThemes).'&act=create&mode=2&pid='.array_search($data[0],$arrThemes),
					'image' => $this->generateImage('layout.gif')
				);
			}
		}
		else
		{
			//show themes
			foreach($arrThemes as $id => $name)
			{
				if(strlen($s) && strripos($name,$s) === false) continue;
				$this->ret[] = array
				(
					'type'  => 'new',
					'name'  => 'new:'.$name,
					'url'   => $this->base.'main.php?do=themes',
					'image' => $this->generateImage('themes.gif')
				);
			}
		}
	}


	/**
	 * Add articles to ret-array
	 * @param str $s
	 */
	protected function addArticles($s)
	{
		$queryAddon = $selectAddon = '';
		if(!$this->User->isAdmin)
		{
			// Get all allowed pages for the current user
			$pagemounts = array();
			foreach ($this->User->pagemounts as $root)
			{
				$pagemounts[] = $root;
				$pagemounts = array_merge($pagemounts, $this->getChildRecords($root, 'tl_page', true));
			}
			$pagemounts = array_unique($pagemounts);

			$queryAddon = 'LEFT JOIN tl_page AS p ON (a.pid = p.id)';
			$selectAddon = ',includeChmod,p.pid,chmod,cuser,cgroup';
		}

		$objArticle = $this->Database->prepare('SELECT a.id,a.title,a.published,a.start,a.stop,a.pid AS pageId
                                                                                                                '.$selectAddon.'
                                                                                                                FROM tl_article AS a
                                                                                                                '.$queryAddon.'
                                                                                                                WHERE a.title LIKE ? ORDER BY a.title')
			->limit($this->limitEach)
			->execute('%'.$s.'%');

		while($objArticle->next())
		{
			// check rights
			if (!$this->User->isAdmin)
			{
				if(!in_array($objArticle->pageId, $pagemounts)) continue;
				if(!$this->User->isAllowed(4, $objArticle->row())) continue;
			}

			$time = time();
			$published = ($objArticle->published && ($objArticle->start == '' || $objArticle->start < $time) && ($objArticle->stop == '' || $objArticle->stop > $time));

			$this->ret[] = array
			(
				'type'  => 'article',
				'name'  => 'a:'.$objArticle->title,
				'url'   => $this->base.'main.php?do=article&table=tl_content&id='.$objArticle->id,
				'image' => $this->generateImage('articles'.($published ? '' : '_').'.gif')
			);

		}


	}


	/**
	 * Add pages to ret-array
	 * @param str $s
	 */
	protected function addPages($s)
	{
		if(!$this->User->isAdmin)
		{
			// Get all allowed pages for the current user
			$pagemounts = array();
			foreach ($this->User->pagemounts as $root)
			{
				$pagemounts[] = $root;
				$pagemounts = array_merge($pagemounts, $this->getChildRecords($root, 'tl_page', true));
			}
			$pagemounts = array_unique($pagemounts);
		}

		$objPage = $this->Database->prepare('SELECT id,title,protected,type,published,start,stop,hide,includeChmod,pid,chmod,cuser,cgroup
                                                                                                 FROM tl_page WHERE title LIKE ? ORDER BY title')
			->limit($this->limitEach)
			->execute('%'.$s.'%');

		while($objPage->next())
		{
			// check rights
			if (!$this->User->isAdmin)
			{
				if(!in_array($objPage->id, $pagemounts)) continue;
				if(!$this->User->isAllowed(1, $objPage->row())) continue;
			}

			$sub = 0;
			$image = $objPage->type.'.gif';
			$objPage->protected = ($objPage->protected || $protectedPage);
			// Page not published or not active
			if ((!$objPage->published || $objPage->start && $objPage->start > time() || $objPage->stop && $objPage->stop < time()))
				$sub += 1;
			// Page hidden from menu
			if ($objPage->hide && !in_array($objPage->type, array('redirect', 'forward', 'root', 'error_403', 'error_404')))
				$sub += 2;
			// Page protected
			if ($objPage->protected && !in_array($objPage->type, array('root', 'error_403', 'error_404')))
				$sub += 4;
			// Get image name
			if ($sub > 0)
				$image = $objPage->type.'_'.$sub.'.gif';

			$this->ret[] = array
			(
				'type'  => 'page',
				'name'  => 'p:'.$objPage->title,
				'url'   => $this->base.'main.php?do=page&act=edit&id='.$objPage->id,
				'image' => $this->generateImage($image, '')
			);

		}

	}


	public function injectJavascript($arrModules)
	{
		if($this->User->quickjump4ward_enabled)
		{
			$GLOBALS['TL_JAVASCRIPT']['autocompleter'] = 'plugins/autocompleter/js/ac_compress.js';
			$GLOBALS['TL_JAVASCRIPT']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.js';
			$GLOBALS['TL_CSS']['quickjump4ward'] = 'system/modules/quickjump4ward/html/quickjump4ward.css';
		}

		return $arrModules;
	}
}
