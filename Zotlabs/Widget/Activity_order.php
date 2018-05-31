<?php

namespace Zotlabs\Widget;

class Activity_order {

	function widget($arr) {

		if(! local_channel())
			return '';

 		if(! feature_enabled(local_channel(),'order_tab')) {
			set_pconfig(local_channel(), 'mod_network', 'order', 0);
			return '';
		}

		$commentord_active = '';
		$postord_active = '';
		$unthreaded_active = '';

		if(x($_GET, 'order')) {
			switch($_GET['order']){
				case 'post':
					$postord_active = 'active';
					set_pconfig(local_channel(), 'mod_network', 'order', 1); 
					break;
				case 'comment':
					$commentord_active = 'active';
					set_pconfig(local_channel(), 'mod_network', 'order', 0);
					break;
				case 'unthreaded':
					$unthreaded_active = 'active';
					set_pconfig(local_channel(), 'mod_network', 'order', 2);
					break;
				default:
					$commentord_active = 'active';
			}
		}
		else {
			$order = get_pconfig(local_channel(), 'mod_network', 'order', 0);
			switch($order) {
				case 0:
					$commentord_active = 'active';
					break;
				case 1:
					$postord_active = 'active';
					break;
				case 2:
					$unthreaded_active = 'active';
					break;
				default:
					$commentord_active = 'active';
			}
		}

		// override order for search results and filer results
		if(x($_GET,'search') || x($_GET,'file')) {
			$unthreaded_active = 'active';
			$commentord_active = $postord_active = 'disabled';
		}

		$cmd = \App::$cmd;

		$filter = '';

		if(x($_GET,'cid'))
			$filter .= '&cid=' . $_GET['cid'];

		if(x($_GET,'gid'))
			$filter .= '&gid=' . $_GET['gid'];

		if(x($_GET,'star'))
			$filter .= '&star=' . $_GET['star'];

		if(x($_GET,'conv'))
			$filter .= '&conv=' . $_GET['conv'];

		if(x($_GET,'file'))
			$filter .= '&file=' . $_GET['file'];


		// tabs
		$tabs = [];

		$tabs[] = [
			'label' => t('Commented Date'),
			'url'=>z_root() . '/' . $cmd . '?f=&order=comment' . $filter,
			'sel'=> $commentord_active,
			'title' => t('Order by last commented date'),
		];
		$tabs[] = [
			'label' => t('Posted Date'),
			'url'=>z_root() . '/' . $cmd . '?f=&order=post' . $filter,
			'sel'=> $postord_active,
			'title' => t('Order by last posted date'),
		];
		$tabs[] = array(
			'label' => t('Date Unthreaded'),
			'url' => z_root() . '/' . $cmd . '?f=&order=unthreaded' . $filter,
			'sel' => $unthreaded_active,
			'title' => t('Order unthreaded by date'),
		);

		$arr = ['tabs' => $tabs];

		call_hooks('network_tabs', $arr);

		$tpl = get_markup_template('common_pills.tpl');

		if($arr['tabs']) {
			return replace_macros($tpl, [
				'$title' => t('Activity Order'),
				'$tabs' => $arr['tabs'],
			]);
		}
		else {
			return '';
		}
	}

}