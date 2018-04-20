<?php

if( !class_exists( 'tmbUpdater' ) ) {


	class tmbUpdater{
	
		public $version = '2.3';
		public $enable_caches = true;
		public $cache_period = 18000; // 5 hrs
		public $update_host = 'https://rudrastyh.com';
		public $update_slug = 'meta505';
		public $plugin_slug = 'true-metabox';
		
		function __construct() {
			add_filter('plugins_api', array($this, 'u_plugin_info'), 20, 3);
			add_filter('site_transient_update_plugins', array( $this, 'u_check_update')); //WP 3.0+
			add_action( 'upgrader_process_complete', array( $this, 'after_update' ), 10, 2 );
		
		
		}
	

		function request(){
	
			if ( $this->enable_caches === false || ( false == $remote = get_transient( 'misha_upgrade_' . $this->update_slug ) ) ) {
				$remote = wp_remote_get(
					$this->update_host . '/plg_api_upd/' . $this->update_slug . '/info.json?checking_for_updates=1',
					array(
						'timeout' => 10,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if ( !is_wp_error( $remote ) 
					&& isset( $remote['response']['code'] ) 
					&& $remote['response']['code'] == 200
					&& !empty($remote['body']) ) {
						set_transient( 'misha_upgrade_' . $this->update_slug, $remote, 43200 );
				} else {
					return false;
				}
				
			
			}
			return $remote;
		
		}
	
		function after_update( $upgrader_object, $options ) {
			if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
				delete_transient( 'misha_upgrade_' . $this->update_slug );
			}
		}
	
		function u_plugin_info( $res, $action, $args ){
		
        
			if( $action !== 'plugin_information' )
				return false;
				
			if( $this->plugin_slug !== $args->slug )
				return $res;
			
		
			
			if( $remote = $this->request() ){
				$remote = json_decode( $remote['body'] );
				$res = new stdClass();
				$res->name = $remote->name;
				$res->slug = $this->plugin_slug;
				$res->version = $remote->version;
				$res->tested = $remote->tested;
				$res->requires = $remote->requires;
				$res->author = '<a href="https://rudrastyh.com">Misha Rudrastyh</a>';
				$res->author_profile = 'https://profiles.wordpress.org/rudrastyh';
				$res->download_link = $remote->download_url;
				$res->trunk = $remote->download_url;
				$res->last_updated = $remote->last_updated;
				$res->sections = array(
					//'description' => $remote->sections->description,
					//'installation' => $remote->sections->installation,
					'changelog' => $remote->sections->changelog
				);
				if( !empty( $remote->sections->screenshots ) ) {
					$res->sections['screenshots'] = $remote->sections->screenshots;
				}
			
				$res->banners = array(
					'low' => $this->update_host . '/plg_api_upd/' . $this->update_slug . '/banner-772x250.jpg',
            		'high' => $this->update_host . '/plg_api_upd/' . $this->update_slug . '/banner-1544x500.jpg'
				);
    	        return $res;
			}

			return false;
	
		}
	
		function u_check_update( $transient ){
			if ( empty($transient->checked ) ) {
            	return $transient;
        	}
        //echo '<pre>' . print_r( $transient, true ) . '</pre>';exit;
        

			if ( $remote = $this->request() ){
				$remote = json_decode( $remote['body'] );
				if( $remote && version_compare( $this->version, $remote->version, '<' )
				&& version_compare($remote->requires, get_bloginfo('version'), '<' ) ) {
					$res = new stdClass();
					$res->slug = $this->plugin_slug;
					$res->plugin = $this->plugin_slug . '/' . $this->plugin_slug . '.php';
					$res->new_version = $remote->version;
					$res->tested = $remote->tested;
					$res->package = $remote->download_url;
					$res->url = $remote->homepage;
					$res->compatibility = new stdClass();
    	       		$transient->response[$res->plugin] = $res;
        	   		//$transient->checked[$res->plugin] = $remote->version;
           		}
 	           
			}
        	//echo '<pre>' . print_r( $transient, true ) . '</pre>';
			//exit;
        	return $transient;
		}
	}



}