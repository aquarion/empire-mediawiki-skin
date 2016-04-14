<?php
/**
 * A MediaWiki skin to use Twitter's Bootstrap.
 * Loosely based on the Bootstrap skin by Aaron Parecki <aaron@parecki.com>
 * but completely rewritten to support Bootstrap 2.0
 * and with a load of additional features.
 *
 * @Version 0.1.0
 * @Author Ian Thomas <ian@wildwinter.net>
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

require_once('includes/SkinTemplate.php');

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class SkinDarqueImperial extends SkinTemplate {
    var $useHeadElement = true;

    function initPage( OutputPage $out ) {
            parent::initPage( $out );
            $this->skinname  = 'darqueimperial';
            $this->stylename = 'darqueimperial';
            $this->template  = 'DarqueImperialTemplate';
    }
    function setupSkinUserCss( OutputPage $out ) {
        //    parent::setupSkinUserCss( $out );
		
            // <meta name="viewport" content="width=device-width, initial-scale=1.0">
            //$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );
			$out->addHeadItem( 'viewport', '<meta name="viewport" content="width=device-width, initial-scale=1.0">' );
			
            // Append to the default screen common & print styles...
            #$out->addStyle( 'http://fonts.googleapis.com/css?family=Gentium+Basic:400,700,400italic,700italic', 'screen, print' );   
            $out->addStyle( 'darqueimperial/css/bootstrap.css', 'screen, print' );
            $out->addStyle( 'darqueimperial/css/bootstrap-responsive.css', 'screen, print' );
            $out->addStyle( 'darqueimperial/site.css', 'screen, print' );
			if(isset($wgSiteCSS)) {
				$out->addStyle( $wgSiteCSS, 'screen, print' );
			}
    }
}

/**
 * @todo document
 * @package MediaWiki
 * @subpackage Skins
 */
class DarqueImperialTemplate extends BaseTemplate {
	/**
	 * @var Cached skin object
	 */
	var $skin;

function breakTitle( &$link, &$title)
{
  if (preg_match('/(.+)\|(.+)/',$link,$match))
  {
    $link=$match[1];
    $title=$match[2];
  }
  else
  {
    $title=$link;
  }
}

function parseMenu($pageTitle)
{
	$nav=array();
	$data = $this->getPageRawText($pageTitle);
	foreach(explode("\n", $data) as $line)
	{
		if(trim($line) == '') continue;

		if(preg_match('/^\*\s*\[\[(.+)\]\]/', $line, $match))
		{
			$nav[] = array('title'=>$match[1], 'link'=>$match[1]);
                }
		elseif(preg_match('/\*\*\s*\[\[(.+)\]\]/', $line, $match))
		{
                  $nav[count($nav)-1]['sublinks'][] = $match[1];
                }
		elseif(preg_match('/\*\*\s*\-\-/', $line, $match))
		{
		  $nav[count($nav)-1]['sublinks'][] = 'sep';
		}
		elseif(preg_match('/\*\*\s*=\s*(.+)\s*\=/', $line, $match))
		{
		  $nav[count($nav)-1]['sublinks'][] = '='.$match[1];
		}
		elseif(preg_match('/^\*\s*(.+)/', $line, $match))
		{
                  $nav[] = array('title'=>$match[1]);
                }
		elseif(preg_match('/=\s*(.+)\s*=/',$line,$match))
		{
		  $nav[]=array('section'=>$match[1]);
		}
        }
	
	$out="";
              
        foreach($nav as $topItem)
	{
		if (array_key_exists('section',$topItem))
		{
			$out.='<li class="nav-header">'.$topItem['section'].'</li>';
			continue;
		}
		$link=$topItem['title'];
		$this->breakTitle($link,$title);
                $pageTitle = Title::newFromText($link);
                if(array_key_exists('sublinks', $topItem))
		{
                  $out.= '<li class="dropdown">';
                    $out.=  '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $title. '<b class="caret"></b></a>';
                    $out.=  '<ul class="dropdown-menu">';
                    foreach($topItem['sublinks'] as $subLink) {
		      if ($subLink=='sep')
		      {
			$out.='<li class="divider"> </li>';
			continue;
	              }
		      if ($subLink{0}=='=')
		      {
			$out.='<li class="nav-header">'.substr($subLink,1).'</li>';
			continue;
                      }
		      $this->breakTitle($subLink,$title);
                      $pageTitle = Title::newFromText($subLink);
                      $out.=  '<li><a href="' . $pageTitle->getLocalURL() . '">' . $title . '</a>';
                    }
                    $out.=  '</ul>';
                  $out.=  '</li>';
                } else {
                      if(is_object($pageTitle)){
                  $out.=  '<li' . ($this->data['title'] == $link ? ' class="active"' : '') . '><a href="' . $pageTitle->getLocalURL() . '">' . $title . '</a></li>';
                       }
                }
	}
	return $out;
}

  /**
   * Template filter callback for Bootstrap skin.
   * Takes an associative array of data set from a SkinTemplate-based
   * class, and a wrapper for MediaWiki's localization database, and
   * outputs a formatted page.
   *
   * @access private
   */        
  public function execute() {
		global $wgUser, $wgSitename, $wgCopyrightLink, $wgCopyright, $wgBootstrap, $wgArticlePath, $wgSiteCSS;
		global $wgRequest;

		
		$requestedAction = $wgRequest->getVal( 'action', 'view' );
		$requestedTitle = $wgRequest->getVal('title');
		
		$isEditing=(strcmp($requestedAction,'edit')==0);
		$isMainPage=(strcasecmp($requestedTitle,'Main_Page')==0);

		$this->skin = $this->data['skin'];
        $skin = $this->data['skin'];
		
		if(! defined("PD_PARENTPAGE")){
			define("PD_PARENTPAGE", $requestedTitle);
		}
		
		$titleBar=$this->parseMenu('Imperial:TitleBar');
		
		$title = $this->getSkin()->getTitle();
        if ( strpos( $title, '/' ) === false ) 
		{
			$this->data['ImpWiztitle'] = $title;
		}
		else
		{
			$this->data['ImpWiztitle'] = strrchr( $title, '/' );
		}
		
		$bc='';
		if ($this->data['breadcrumbs'])
		{
			$bc=$this->data['breadcrumbs'];
			$bc=str_replace('<a','<li><a',$bc);
			$bc=str_replace('/a> &gt;','/a><span class="divider">/</span></li>',$bc);
			$bc=str_replace('<strong','<li><strong',$bc);
	                $bc=preg_replace('/\/strong\>(.*)$/','/strong></li>',$bc); 
		}
		
        // Suppress warnings to prevent notices about missing indexes in $this->data
        wfSuppressWarnings();
 
		// Output HTML Page
        $this->html( 'headelement' );
?>

<!-- Body content starts here -->  

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <i class="icon-search icon-white"></i>
          </a>
	  <a class="brand" href="<?php echo $this->data['nav_urls']['mainpage']['href'] ?>"><?php echo $wgSitename ?></a>
          <div class="nav-collapse">
           	<form class="pull-right navbar-search" action="<?php $this->text( 'wgScript' ); ?>">
                        <input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
                        <?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
                </form>
		<ul class="nav"><?php echo $titleBar;?></ul>
<?php
	if($wgUser->isLoggedIn())
	{?>
	  <ul<?php $this->html('userlangattributes') ?> class="nav pull-right"><?php
   		if ( count( $this->data['personal_urls'] ) > 0 ) {
  		?>
	    <li class="dropdown">
	      <a class="dropdown-toggle" href="#" data-toggle="dropdown"><?php echo $wgUser->getName(); ?><b class="caret"></b></a>
	      <ul class="dropdown-menu">
	      <?php foreach($this->data['personal_urls'] as $item): ?>
	        <li <?php echo $item['attributes'] ?>><a href="<?php echo htmlspecialchars($item['href']) ?>"<?php echo $item['key'] ?><?php if(!empty($item['class'])): ?> class="<?php echo htmlspecialchars($item['class']) ?>"<?php endif; ?>><?php echo htmlspecialchars($item['text']) ?></a></li>
	      <?php endforeach; ?>
              </ul>
            </li>
  		<?php
  		}
		
	}
        ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	
    <div id="article" class="container-fluid">
      <div class="row-fluid">

        <div id="leftbar" class="span2">
<?php
$logo = wfFindFile(Title::makeTitle(NS_IMAGE, 'Logo.jpg'));
if ($logo) {?>
	<div id="logo">
	<img src="<?php echo $logo->getURL();?>"/>
	</div>	
<?php } ?>	
<?php if ($wgUser->isLoggedIn()){?>
	<div id="pageButtons">
<?php
$this->renderPageButtons($isEditing);
?>	</div>
<?php } ?>
          <div class="well sidebar-nav">
	<?php 
            $this->includePage('Imperial:LeftBar');
        ?>
          </div><!--/.well -->
        </div><!--/span-->
	
	
        <div class="span10">
		
	<?php echo $this->getCategories();?>
	      
	  <div class="row-fluid">
	    <?php if( $this->data['sitenotice'] ) { ?><div id="siteNotice" class="alert alert-block alert-message warning"><?php $this->html('sitenotice') ?></div><?php } ?>

            <div id="page-title" class="page-header">
              <h1><?php echo $this->data['ImpWiztitle']; ?> <small><?php $this->html('subtitle') ?></small></h1>
	      <?php if ($this->data['breadcrumbs']) { ?>
	      <ul class="breadcrumb"><?php echo $bc;?></ul>
	      <?php } ?>
            </div>

	  </div>

<!-- Page content starts here -->  
	  <div class="row-fluid">
	    <?php $this->html( 'bodytext' ) ?>
	  </div>
        </div><!--/span-->
      </div><!--/row-->
	
    </div><!--/.fluid-container-->
    
    <div id="footer" class="container-fluid">
        <?php
        $this->includePage('Imperial:Footer');
        ?>
    </div>
	
<div style="display:none;">
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/js/bootstrap.js"></script>
    <script src="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/site.js"></script>
	
</div>
		<?php $this->html( 'dataAfterContent' ); ?>
		<?php $this->printTrail(); ?>
  </body>
</html>
<?php
	}
	
	function getPageRawText($title) {
    $pageTitle = Title::newFromText($title);
    if(!$pageTitle->exists()) {
      return 'Create the page [['.$title.']]';
    } else {
      $article = new Article($pageTitle);
      return $article->getRawText();
    }
	}
	
	function includePage($title) {
    global $wgParser, $wgUser;
    $pageTitle = Title::newFromText($title);
    if(!$pageTitle->exists()) {
      echo 'The page [[' . $title . ']] was not found.';
    } else {
      $article = new Article($pageTitle);
      $wgParserOptions = new ParserOptions($wgUser);
      $parserOutput = $wgParser->parse($article->getRawText(), $pageTitle, $wgParserOptions);
      echo $parserOutput->getText();
    }
	}
	
	function renderPageButton($key,$icon)
	{
		if (!array_key_exists($key,$this->data['content_actions']))
			return;
		$action=$this->data['content_actions'][$key];
		echo '<a class="btn" href="'.htmlspecialchars($action['href']).'" title="'.htmlspecialchars($action['text']).'"><i class="'.$icon.'"></i></a>';
	}
	
	function renderPageButtons($isEditing)
	{
		if ( count( $this->data['content_actions'])==0 )
			return false;
		
		echo '<div class="btn-group">';
		if (!$isEditing)
			$this->renderPageButton('edit','icon-edit');
		$this->renderPageButton('history','icon-time');
		$this->renderPageButton('delete','icon-trash');
		$this->renderPageButton('move','icon-move');
		$this->renderPageButton('protect','icon-lock');
		$this->renderPageButton('watch','icon-eye-open');
		$this->renderPageButton('unwatch','icon-eye-close');
		$this->renderPageButton('talk','icon-comment');
		echo '</div>';

		return true;
		
	}
	
        function getCategories() {
                $catlinks = $this->getCategoryLinks();
                if( !empty( $catlinks ) )
		{
                        return '<div id="pageCategories"><ul class="pager">'.$catlinks.'</ul></div>';
                }
        }
 
       	function getCategoryLinks() {
		global $wgOut;

		$out = $wgOut;

		if ( count( $out->mCategoryLinks ) == 0 ) {
			return '';
		}

		$embed = "<li>";
		$pop = "</li>";

		$allCats = $out->getCategoryLinks();
		$s = '';
		$colon = wfMsgExt( 'colon-separator', 'escapenoentities' );

		if ( !empty( $allCats['normal'] ) ) {
			$s.= $embed . implode( "{$pop}{$embed}" , $allCats['normal'] ) . $pop;
		}

		return $s;
	}
}

