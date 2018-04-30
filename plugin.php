<?php

class pluginBloggersSidebar extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'pagesLabel'=>'Latest posts',
			'pagesCount'=>1,
			'stickyLabel'=>'Important',
			'stickyCount'=>1,
			'enableStickies'=>true,
			'enableStickiesCover'=>true,
		);
	}

	public function form()
	{
		global $Language;

    $html = '<style>
      #jsformplugin div {margin:0!important;display:block!important;}
      #pluginBloggersSidebar {background:#fff;padding:2rem; margin:0rem; box-sizing: border-box; overflow:hidden; max-width:960px; display:block; width: auto; font-size:14px;}
      #pluginBloggersSidebar p {margin:0; padding:0; line-height135%;}
      .plugin-pod-header {background:#cfd7df; color:#444; padding:2rem 1rem; margin:1rem 1rem 0rem 1rem;box-sizing: border-box;display:block; width: auto}
      .plugin-pod {border:1px solid #eff3f4; background:#fafbfd; color:#323c46; padding:2rem; margin:0 1rem 1rem 1rem;box-sizing: border-box;display:block; width: auto}
      #pluginBloggersSidebar h1, #pluginBloggersSidebar h2, #pluginBloggersSidebar h3, #pluginBloggersSidebar h4, #pluginBloggersSidebar h5, #pluginBloggersSidebar h6 {line-height:125%;  padding:0;}
      .plugin-pod-header h1 {color:#222;font-size:24px;margin:0; font-weight:bold}
      .plugin-pod-header h2 {color:#222;font-size:22px;margin:0; font-weight:bold}
      .plugin-pod-header h3 {color:#222;font-size:20px;margin:0; font-weight:normal}
      #pluginBloggersSidebar label {font-weight:bold}
      </style>';

    $html .= '<div id="pluginBloggersSidebar">';

    $html .= '<div class="plugin-pod-header">';
      $html .= '<h1>Bloggers Sidebar</h1>';
      $html .= '<h3>Display latest happenings on your site with style</h3>';
    $html .= '</div>';

		$html .= '<div class="plugin-pod">';
		$html .= '<h3>Published pages</h3>';
    $html .= '<p>Display latest posts from each category in the sidebar.</p>';
		$html .= '<hr/>';
		$html .= '<p>'.$Language->get('Label').' <input name="pagesLabel" type="text" value="'.$this->getValue('pagesLabel').'"> ';
    $html .= 'How many latest posts to show: <input id="pagesCount" name="pagesCount" type="number"  value="'.$this->getValue('pagesCount').'" max="10" min="0" >.</p>';
		$html .= '</div>';
		$html .= '<div class="plugin-pod">';
		$html .= '<h3>Sticky pages</h3>';
		$html .= '<p>Display sticky pages above latest posts. ';
    $html .= '<select name="enableStickies">';
    $html .= '<option value="true" '.($this->getValue('enableStickies')===true?'selected':'').'>'.$Language->get('enabled').'</option>';
    $html .= '<option value="false" '.($this->getValue('enableStickies')===false?'selected':'').'>'.$Language->get('disabled').'</option>';
    $html .= '</select></p>';
		$html .= '<hr/>';
		$html .= '<p>'.$Language->get('Label').' <input name="stickyLabel" type="text" value="'.$this->getValue('stickyLabel').'">. ';
		$html .= 'How many latest sticky posts to show: <input id="stickyCount" name="stickyCount" type="number"  value="'.$this->getValue('stickyCount').'" max="10" min="0" >. ';
		$html .= 'Show cover images if available: <select name="enableStickiesCover">';
    $html .= '<option value="true" '.($this->getValue('enableStickiesCover')===true?'selected':'').'>'.$Language->get('yes').'</option>';
    $html .= '<option value="false" '.($this->getValue('enableStickiesCover')===false?'selected':'').'>'.$Language->get('no').'</option>';
    $html .= '</select></p>';
		$html .= '</div>';
    $html .= '</div>';
		return $html;
	}

	public function siteSidebar()
	{
    global $L;
    global $Url;
    global $Page;
		global $dbPages;

		if (($this->getDbField('enableStickies')) && ($Page->status() !== 'sticky' ) && ($Url->whereAmI()=='page') || ($Url->whereAmI()=='category')) {
			$stickyPages = $dbPages->getStickyDB();
			if (count($stickyPages) >= 1 ) {
			echo'<div class="plugin plugin-seolatest">';
			if ($this->getValue('stickyLabel')) {
				echo'<h2 class="plugin-label">'.$this->getValue('stickyLabel').'</h2>';
				echo'<hr/>';
				}
				$SCount = 0;
				foreach ($stickyPages as $stickyKey) {
					$stickpage = buildPage($stickyKey);
					if ($this->getDbField('enableStickiesCover')) {
					if ($stickpage->coverImage()){
						echo'<div class="cover-image-thumb">';
							echo'<a href="'.$stickpage->permalink().'"><img class="scale-me" alt="'.$stickpage->title().'" src="'.HTML_PATH_UPLOADS_THUMBNAILS.$stickpage->coverImage(false).'"/></a>';
						echo'</div>';
					}
					}
					echo'<h5><a href="'.$stickpage->permalink().'">'.$stickpage->title().'</a></h5>';
					echo'<hr/>';
					$SCount++;
					 if ($SCount == $this->getDbField('stickyCount')){
							 break;
					 }
				}
			echo'</div>';
			}
		}

			$categories = getCategories();
			echo'<div class="plugin plugin-seolatest">';
			if ($this->getValue('pagesLabel')) {
	      echo'<h2 class="plugin-label">'.$this->getValue('pagesLabel').'</h2>';
				echo'<hr/>';
	    }
      foreach ($categories as $category) {
          echo'<h2 class="plugin-label"><a href="'.$category->permalink().'">'.$category->name().'</a></h2>';
					echo'<hr/>';
          echo'<ul>';
          $childrinos = $category->pages();
                $Count = 0;
          foreach ($childrinos as $pageKey) {
              $catpage = buildPage($pageKey);
              echo'<li><small><time class="published" datetime="'.$catpage->dateRaw().' ">'.$catpage->date().'</time></small> <a href="'.$catpage->permalink().'">'.$catpage->title().'</a></li>';
              $Count++;
               if ($Count == $this->getDbField('pagesCount')){
                   break;
               }
          }
          echo'</ul>';
					echo'<hr/>';
        }

			echo'</div>';
	}
}
