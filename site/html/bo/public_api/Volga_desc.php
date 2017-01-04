			<div id="Volga_Introduction" class="section desc">
				<h2>Introduction</h2>
				<p>
					<name>Volga</name> is a PHP SDK for developers. It's ready to use. You just need to edit some files, create your database and so on...
					<i>(See the <a href="#" onclick="javascript: openSection('Installation');">Installation section</a> to quickly install it)</i>
				</p>
				
				<p>
					Take a look to the folder structure of the source code as you will have to insert your own file in this structure. 
					For security reason full paths won't be given even if these files are protected <i>(search them by your own)</i>. 
					Each new page on your Website will be holded.
				</p>
			</div>
			
			<div id="Volga_Features" class="section desc">
				<h2>Features</h2>
				<p>
					Volga SDK has all the features easy to use:
				</p>
				<ul>
					<li><label>URL Rewriting</label></li>
					<li><label>Caching file</label></li>
					<li><label>Using GZIP compression</label></li>
					<li><label>Multi-languages support</label></li>
					<li><label>Back Office with basic-update features</label></li>
					<li><label>Advanced JavaScript functions (including Prototype library + Volga JS Core)</label></li>
					<li><label>Easy database transferts and error log</label></li>
					<li><label>Advanced Sessions support (inluding multi-sessions)</label></li>
					<li><label>Remember connections/visits and pages viewed</label></li>
				</ul>
			</div>
			
			<div id="Volga_Installation" class="section desc">
				<h2>Installation</h2>
				<p>
					This SDK has no installation program, but what you have to do is to copy all files to a remote directory and get into the 'conf' folder and edit files.
					The main file to edit first is 'classes.conf.php'. For instance you can change the alias, the DB connection settings, parameters for URL Rewriting, etc.
				</p>
			</div>
			
<?php
	if( Common::isLocalContext() )
	{
?>
			<div id="Volga_Description" class="section desc">
				<h2>Description</h2>
				<p>
					Here is information about creating your own pages, using the features and so on...
				</p>
				
				<h3>Create new page</h3>
				<p>
					This is the same process for both sides (Back Office and front site). You need first to create a folder within the folder 'pages_php' or 'pages_admin' ('xxx' for the example)
					You then need to create a PHP file that will be the Webpage: '<u>xxx.php</u>'.
				</p>
				<p>
					The associated CSS and JS file will be in the same folder with the following names: '<u>xxx.css.php</u>' and '<u>xxx.js.php</u>'. These files are optional.
					The final 'php' extension is optional but you have to set it as a PHP file is you need to retrieve $_GET values or the computed values. 
					You do not have anything to do then for these file to be called by the &lt;head /&gt; tag.
					Check the <a href="#" onClick="javascript: openPhpClass('Head');">Head Class</a>, you can then send paramtyers to these files and modify properties
					<i>(cf <a href="#Head::setLinkedCSSProperties" onClick="javascript: openPhpClass('Head');">Head::setLinkedCSSProperties()</a>
					and <a href="#Head::setLinkedJSProperties" onClick="javascript: openPhpClass('Head');">Head::setLinkedJSProperties()</a>)</i>. 
					The best place to call these two methods is from the 'xxx.conf.php' file. Read below...
				</p>
				<p>
					Two optional files can be created: '<u>xxx.conf.php</u>' and '<u>xxx.data.php</u>'. 
					The last one is called by 3 files: the main page ('<u>xxx.php</u>'), the CSS and the JS file (if they are dynamic: have a PHP extension).
					This file is used t create constants or do some initializations used only by the current page (the one you're creating). 
					Data are then shared between these 3 files, that means that they are computed up to 3 time for a single page load. 
					So do not do to do stuffs time-consuming.<br />
					
					'<u>xxx.conf.php</u>' is called before creating the header (&lt;head /&gt; tag). 
					This is the best place to handle forms results or initialize stuffs for the main page, to separate logic and display. 
					Here you can change the related CSS and JS parameters and preperties <i>(cf above)</i> or include other CSS/JS files
					<i>(cf <a href="#Head::addCSS" onClick="javascript: openPhpClass('Head');">Head::addCSS()</a>
					and <a href="#Head::addJS" onClick="javascript: openPhpClass('Head');">Head::addJS()</a>)</i>					
					You can also change the page name in here thanks to the <a href="#" onClick="javascript: openPhpClass('Head');">Head Class</a>.
				</p>
				<p>
					<b>To be completed... About URL Rewriting and page title</b>.
				</p>
				
				
			</div>
<?php
	}
?>