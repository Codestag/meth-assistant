<?php

add_action('add_meta_boxes', 'stag_metabox_team');

function stag_metabox_team(){
	$meta_box = array(
		'id'          => 'stag-metabox-team',
		'title'       =>  __( 'Team Member Info', 'meth' ),
		'description' => __( 'Enter team member information.', 'meth' ),
		'page'        => 'team',
		'context'     => 'normal',
		'priority'    => 'high',
		'fields'      => array(
			array(
				'name' =>  __( 'Role', 'meth' ),
				'desc' => __( 'Enter the current role of the team member.', 'meth' ),
				'id'   => '_stag_team_member_role',
				'type' => 'text',
				'std'  => ''
			),
			array(
				'name' =>  __( 'Bio', 'meth' ),
				'desc' => __( 'Enter short bio of the team member.', 'meth' ),
				'id'   => '_stag_team_info',
				'type' => 'textarea',
				'std'  => '',
				'rows' => '4'
			),
		)
	);

    stag_add_meta_box( $meta_box );
}
