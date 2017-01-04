<div class="autocomplete" id="php_fcts_list" style="display:none"></div>
<table id="frame" class="container">
	<tr class="title">
		<td colspan="2">
			<h1>Volga API for developers</h1>
		</td>
	</tr>
	<tr style="position: relative;">
		<td class="menu" style="position: relative;">
			<h4>Volga SDK</h4>
			<ul>
<?php
		foreach($API_sections as $section)
			echo "\t\t\t\t<li><a id='$section'>$section</a></li>\n";
?>
			</ul>
			
			<hr />
			
			<h4>PHP Classes</h4>
			
			<input id="php_search" autocomplete="off" size="20" type="text" value="" />
			<ul style="margin-top: 3em;">
<?php
		foreach($VolgaClasses as $class)
			echo "\t\t\t\t<li><a id=".LINK_SUFFIX.$class->name.">".$class->name."</a></li>\n";
?>
			</ul>
		</td>
		<td class="desc">
			<div id="TopLink"><a onClick="javascript: scrollToTop();">Top</a></div>
<?php

		include "Volga_desc.php";
		
		foreach($VolgaClasses as $i => $class)
		{
			$id = DIV_SUFFIX.$class->name;
?>
			<div class="section class<?php echo ($class->isInterface ? ' cls_int' : ''); ?>" id="<?php echo $id; ?>">
				<a name="<?php echo TOP_LINK_SUFFIX.$class->name; ?>"></a>
				<h1><?php echo ($class->isInterface ? 'Interface' : 'Class') .' '.$class->name;?> <file>(<?php echo $class->phpFile; ?>)</file></h1>
<?php
			if( $class->inherits )
				echo "\t\t\t\t<h2>Inherits from <a onclick=\"javascript: openPhpClass('$class->inherits');\">$class->inherits</a>. <inherits>Maybe some functions inherit from this class !</inherits></h2>\n";
?>
				<hr /><!-- Class Description -->
				<div class="tab desc">
					<h3>Description:</h3>
					<p><?php echo $class->getComments(); ?></p>
				</div>
				
				<hr /><!-- Method Summary -->
				<div class="tab summary">
					<h3>Method summary</h3>
					<ul>
<?php
					foreach($class->functions as $fct)
					{
?>
						<li><a onClick="javascript: scrollToFunction('<?php echo $fct->getAnchorName(); ?>');"><?php echo $fct->getFullName(false); ?></a>(<args><?php echo $fct->args; ?></args>)</li>
<?php
					}
?>
					</ul>
				</div>
				
<?php
			if( count($class->constants) )
			{
?>
				<hr /><!-- List of Constants -->
				<div class="tab constant">
					<h3>Constants</h3>
					<ul>
<?php
					foreach($class->constants as $cst => $val)
					{
?>
						<li><label title="<?php echo String::toJS($val); ?>"><?php echo $cst; ?></label></li>
<?php
					}
?>
					</ul>
				</div>
<?php
			}
?>

				<hr /><!-- List of Functions -->
<?php
			foreach($class->functions as $i=>$fct)
			{
?>
				<div class="tab function<?php if($i) echo ' no_first'; if($fct->isInherited()) echo ' inherited' ?>"<?php if($fct->isInherited()) echo ' title="This function is inherited from another class"'; ?>>
					<a name="<?php echo $fct->getAnchorName(); ?>"></a>
					<h3><?php echo $fct->getFullName(); ?></h3>
					<?php echo $fct->getComments()."\n"; ?>
				</div>
<?php
			}
?>

<?php
			if( count($class->classesNeeded) )
			{
?>
				<hr /><!-- Required Classes -->
				<div class="tab require">
					<h3>Required Classes:</h3>
					<p><?php
					foreach($class->classesNeeded as $i=>$needed)
					{
						if($i)
							echo ', ';
						echo "<a onClick=\"javascript: openPhpClass('$needed'); \">$needed</a>";
					}
?></p>
				</div>
<?php
			}
?>
				
			</div>
<?php
		}
?>
		</td>
	</tr>
</table>
