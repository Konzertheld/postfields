<?php

/**
* Post Fields - A plugin to display additional fields on the publish page
**/
class postfields extends Plugin
{
	/**
	* Required Plugin Information
	*/
	public function info()
	{
		return array(
			'name' => 'Post Fields',
			'version' => '1.0',
			'url' => 'http://habariproject.org',
			'author' => 'Habari Community',
			'authorurl' => 'http://habariproject.org',
			'license' => 'Apache License 2.0',
			'description' => 'Display additional fields on the post page in the tabs to let authors add additional metadata to their posts.',
			'copyright' => '2008'
		);
	}

	/**
	* Add actions to the plugin page for this plugin
	*
	* @param array $actions An array of actions that apply to this plugin
	* @param string $plugin_id The string id of a plugin, generated by the system
	* @return array The array of actions to attach to the specified $plugin_id
	*/
	public function filter_plugin_config($actions, $plugin_id)
	{
		if ($plugin_id == $this->plugin_id()){
			$actions[] = 'Configure';
		}

		return $actions;
	}

	/**
	* Respond to the user selecting an action on the plugin page
	*
	* @param string $plugin_id The string id of the acted-upon plugin
	* @param string $action The action string supplied via the filter_plugin_config hook
	*/
	public function action_plugin_ui($plugin_id, $action)
	{
		if ($plugin_id == $this->plugin_id()){
			switch ($action){
				case 'Configure' :
					$ui = new FormUI('postfields');
					$ui->append('textmulti', 'fields', 'postfields__fields', 'Additional Fields:');
					$ui->append('submit', 'submit', 'Submit');
					$ui->out();
					break;
			}
		}
	}

	/**
	* Add additional controls to the publish page tab
	*
	* @param array $controls An associative array of HTML that will appear in the tabs on the publish page
	* @param Post $post The post being edited
	* @return array The modified $controls array
	**/
	public function filter_publish_controls($controls, $post)
	{
		$fields = Options::get('postfields__fields');
		$output = '';
		$control_id = 0;
		foreach($fields as $field) {
			$control_id++;
			$value = isset($post->info->{$field}) ? $post->info->{$field} : '';
			$output .= <<< FIELD_OUT
				<div class="container">
					<p class="column span-5"><label for="postfield_{$control_id}">{$field}</field></p>
					<p class="column span-14 last"><input type="text" id="postfield_{$control_id}" name="postfield[{$field}]" value="{$value}"></p>
				</div>
FIELD_OUT;
		}

		$controls['Additional Fields'] = $output;
		return $controls;
	}

	/**
	* Modify a post before it is updated
	*
	* @param Post $post The post being saved, by reference
	*/
	public function action_post_update_before($post)
	{
		if(isset($_POST['postfield'])) {
			$fields = Options::get('postfields__fields');
			foreach($fields as $field) {
				$post->info->{$field} = isset($_POST['postfield'][$field]) ? $_POST['postfield'][$field] : '';
			}
		}
	}

	/**
	* Modify a post before it is updated
	*
	* @param Post $post The post being saved, by reference
	*/
	public function action_post_insert_before($post)
	{
		$this->action_post_update_before($post);
	}

}

?>