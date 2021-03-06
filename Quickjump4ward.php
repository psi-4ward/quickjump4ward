<?php

/**
 * Quickjump4ward
 * Quickly jump to various contao pages and settings
 *
 * @copyright  4ward.media 2014 <http://www.4wardmedia.de>
 * @author     Christoph Wiechert <wio@psitrax.de>
 * @package    quickjump4ward
 * @license    LGPL
 * @filesource
 */


namespace Psi\Quickjump4ward;

class Quickjump4ward extends \Backend {
       
	protected $ret = array();
	protected $limitEach = 5;

	public function __construct()
	{
		$this->import('\BackendUser','User');
		if(!$this->User->id) $this->User->authenticate();

		parent::__construct();

		$this->base = $this->Environment->base.((substr($this->Environment->base,-1) != '/') ? '/':'').'contao/';

	}

	public function getResult($s,$type = 'all')
	{
		$s = urldecode($s);

		// tipple the limitEach if weve a specific type
		if($type !='all') $this->limitEach *= 3;

		$autoTypes = $this->User->quickjump4ward;
		if(!is_array($autoTypes)) $autoTypes = array();

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
	 * @param string $s
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

		if(in_array('repository', \ModuleLoader::getActive()))
		{
			$arrFunc[] = array
			(
				'type'  => 'function',
				'name'  => 'f:Database update',
				'url'   => $this->base.'main.php?do=repository_manager&update=database',
				'image' => $this->generateImage('system/modules/repository/themes/default/images/dbcheck16.png')
			);
			$arrFunc[] = array
			(
				'type'  => 'function',
				'name'  => 'f:Extension installieren',
				'url'   => $this->base.'main.php?do=repository_manager&install=extension',
				'image' => $this->generateImage('system/modules/repository/themes/default/images/install16.png')
			);
		}

		if(in_array('!composer', \ModuleLoader::getActive()))
		{
			$arrFunc[] = array
			(
				'type'  => 'function',
				'name'  => 'f:Database update',
				'url'   => $this->base.'main.php?do=composer&update=database',
				'image' => $this->generateImage('system/modules/%21composer/assets/images/database_update.png')
			);
		}

		$arrFunc[] = array
		(
			'type'  => 'function',
			'name'  => 'f:Cache löschen',
			'url'   => $this->base.'main.php?do=maintenance&quickjump4ward=doClearCache',
			'image' => $this->generateImage('cache.gif')
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
				$pagemounts = array_merge($pagemounts, $this->Database->getChildRecords($root, 'tl_page', true));
			}
			$pagemounts = array_unique($pagemounts);

			$selectAddon = ',includeChmod,p.pid,chmod,cuser,cgroup';
		}

		$objArticle = $this->Database->prepare('SELECT a.id,a.title,a.published,a.start,a.stop,a.pid AS pageId,p.pid AS pagePid
                                               '.$selectAddon.'
                                               FROM tl_article AS a
                                               LEFT JOIN tl_page AS p ON (a.pid = p.id)
                                               WHERE a.title LIKE ? ORDER BY a.title')
			->limit($this->limitEach)
			->execute('%'.$s.'%');

		// make duplicates unique (see #8)
		$arrArticles = $objArticle->fetchAllAssoc();
		foreach($arrArticles as $k => $article)
		{
			foreach($arrArticles as $j => $a2)
			{
				if($article['id'] != $a2['id'] && $article['title'] == $a2['title'])
				{
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($article['pageId']);
					$arrArticles[$k]['title'] = $objParent->title.' > '.$article['title'];
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($a2['pageId']);
					$arrArticles[$j]['title'] = $objParent->title.' > '.$a2['title'];
				}
			}
		}
		foreach($arrArticles as $k => $article)
		{
			foreach($arrArticles as $j => $a2)
			{
				if($article['id'] != $a2['id'] && $article['title'] == $a2['title'])
				{
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($article['pagePid']);
					$arrArticles[$k]['title'] = $objParent->title.' > '.$article['title'];
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($a2['pagePid']);
					$arrArticles[$j]['title'] = $objParent->title.' > '.$a2['title'];
				}
			}
		}

		foreach($arrArticles as $article)
		{
			// check rights
			if (!$this->User->isAdmin)
			{
				if(!in_array($article['pageId'], $pagemounts)) continue;
				if(!$this->User->isAllowed(4, $article)) continue;
			}

			$time = time();
			$published = ($article['published'] && ($article['start'] == '' || $article['start'] < $time) && ($article['stop'] == '' || $article['stop'] > $time));

			$this->ret[] = array
			(
				'type'  => 'article',
				'name'  => 'a:'.$article['title'],
				'url'   => $this->base.'main.php?do=article&table=tl_content&id='.$article['id'],
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
				$pagemounts = array_merge($pagemounts, $this->Database->getChildRecords($root, 'tl_page', true));
			}
			$pagemounts = array_unique($pagemounts);
		}

		$objPage = $this->Database->prepare('SELECT id,title,protected,type,published,start,stop,hide,includeChmod,pid,chmod,cuser,cgroup
                                                                                                 FROM tl_page WHERE title LIKE ? ORDER BY title')
			->limit($this->limitEach)
			->execute('%'.$s.'%');

		// make duplicates unique (see #8)
		$arrPages = $objPage->fetchAllAssoc();
		foreach($arrPages as $k => $page)
		{
			foreach($arrPages as $j => $p2)
			{
				if($page['id'] != $p2['id'] && $page['title'] == $p2['title'])
				{
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($page['pid']);
					$arrPages[$k]['title'] = $objParent->title.' > '.$page['title'];
					$objParent = $this->Database->prepare('SELECT title FROM tl_page WHERE id=?')->execute($p2['pid']);
					$arrPages[$j]['title'] = $objParent->title.' > '.$p2['title'];
				}
			}
		}

		foreach($arrPages as $page)
		{
			// check rights
			if (!$this->User->isAdmin)
			{
				if(!in_array($page['id'], $pagemounts)) continue;
				if(!$this->User->isAllowed(1, $page)) continue;
			}

			$sub = 0;
			$image = $page['type'].'.gif';
			$page['protected'] = ($page['protected'] || $protectedPage);
			// Page not published or not active
			if ((!$page['published'] || $page['start'] && $page['start'] > time() || $page['stop'] && $page['stop'] < time()))
				$sub += 1;
			// Page hidden from menu
			if ($page['hide'] && !in_array($page['type'], array('redirect', 'forward', 'root', 'error_403', 'error_404')))
				$sub += 2;
			// Page protected
			if ($page['protected'] && !in_array($page['type'], array('root', 'error_403', 'error_404')))
				$sub += 4;
			// Get image name
			if ($sub > 0)
				$image = $page['type'].'_'.$sub.'.gif';

			$this->ret[] = array
			(
				'type'  => 'page',
				'name'  => 'p:'.$page['title'],
				'url'   => $this->base.'main.php?do=page&act=edit&id='.$page['id'],
				'image' => $this->generateImage($image, '')
			);

		}

	}


	public function injectJavascript($arrModules)
	{
		if($this->User->quickjump4ward_enabled)
		{
			$GLOBALS['TL_JAVASCRIPT']['autocompleter'] = 'assets/mootools/autocompleter/js/ac_compress.js';
			$GLOBALS['TL_JAVASCRIPT']['quickjump4ward'] = 'system/modules/quickjump4ward/public/quickjump4ward.js';
			$GLOBALS['TL_CSS']['quickjump4ward'] = 'system/modules/quickjump4ward/public/quickjump4ward.css';
		}

		$GLOBALS['TL_MOOTOOLS'][] = '<script>window.QUICKJUMP4WARD = {mod: "'.$this->User->quickjump4ward_key_modifier.'", key: "'.$this->User->quickjump4ward_key.'"};</script>';

		return $arrModules;
	}
}
