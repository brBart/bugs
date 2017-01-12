<?php
$active_projects =Project\User::active_projects();
if(count($active_projects)>1){
?>
<form class="projects_selector">
<fieldset><label for="projects_select"><?php echo __('tinyissue.select_a_project');?></label>
<select name="projects_select" id="projects_select"  onchange="if (this.value) window.location.href=this.value">
<?php 
	$Proj = array();
	$SansAccent = array();
	foreach($active_projects as $row) {
		$Proj[$row->to()] = $row->name.'&nbsp;<span class="info-open-issues" title="Open this project"></span>';
	}
	foreach ($Proj as $ind => $val ){
		$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
		$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);		
	}
	asort($SansAccent); 

	foreach($SansAccent as $ind => $val) {
		$selected = ($ind == Project::current()->id) ? 'selected':'';
		echo '<option value="'.$ind.'">'.$Proj[$ind].'</option>';
	 } 
?>
</select>
</fieldset>
</form>
<?php
}
?>

<h2>
	<?php if(Auth::user()->permission('project-modify')): ?>
	<a href="<?php echo Project::current()->to('edit'); ?>" class="edit"><?php echo __('tinyissue.edit');?></a>
	<?php endif; ?>

	<?php echo HTML::link(Project::current()->to(), Project::current()->name); ?>
	<span><?php echo __('tinyissue.assign_users_and_edit_the_project');?></span>
</h2>

<ul>
	<li><a href="<?php echo Project::current()->to('issues'); ?>?tag_id=1"><?php echo Project::current()->count_open_issues(); ?> <?php echo __('tinyissue.open_issues');?></a></li>
	<li><a href="<?php echo Project::current()->to('issues'); ?>?tag_id=2"><?php echo Project::current()->count_closed_issues(); ?> <?php echo __('tinyissue.closed_issues');?></a></li>
</ul>

<h2>
	<?php echo __('tinyissue.assigned_users');?>
	<span><?php echo __('tinyissue.assigned_users_description');?></span>
</h2>

<ul class="sidebar-users">
<?php foreach(Project::current()->users()->get() as $row): ?>

	<li id="project-user<?php echo $row->id; ?>">
		<?php if(Auth::user()->permission('project-modify')): ?>
		<a href="javascript:void(0);" onclick="remove_project_user(<?php echo $row->id; ?>, <?php echo Project::current()->id; ?>);" class="delete"><?php echo __('tinyissue.remove');?></a>
		<?php endif; ?>
		<?php echo $row->firstname . ' ' . $row->lastname; ?>
	</li>
<?php endforeach; ?>
</ul>

<?php if(Auth::user()->permission('project-modify')): ?>

	<input type="text" id="add-user-project" placeholder="<?php echo __('tinyissue.assign_a_user');?>" onmouseover="init_sidebar_autocomplete(<?php echo Project::current()->id; ?>);" />

<?php endif; ?>

<?php
	$project_WebLnks = \DB::table('projects_links')->where('id_project', '=', Project::current()->id)->order_by('category','ASC')->get();
	$WebLnk = array();
	foreach($project_WebLnks as $WebLnks) { 
		if (trim($WebLnks->desactivated) == '') { $WebLnk[$WebLnks->category] = $WebLnks->link; } 
	}
if (count($WebLnk) > 0 ) {
?>
<h2>
	<?php echo __('tinyissue.website_title');?>
	<span><?php echo __('tinyissue.website_description');?></span>
</h2>
<?php
	echo '<ul>';
	foreach($WebLnk as $categ => $link) { 
		echo '<li><a href="'.$link.'" class="links" target="_blank">'.__('tinyissue.website_'.$categ).'</a></li>'; 
	}
	echo '</ul>';
}
?>
