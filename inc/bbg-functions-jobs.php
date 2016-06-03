<?php 

	function getJobs() {
		$jobsFilepath = get_template_directory() . "/jobcache.json";
		if ( fileExpired($jobsFilepath, 90)  ) {  //1440 min = 1 day
			$jobsUrl="https://api.usa.gov/jobs/search.json?organization_ids=IB00";
			$result=fetchUrl($jobsUrl);
			file_put_contents($jobsFilepath, $result);
		} else {
			$result=file_get_contents($jobsFilepath);
		}
		$jobs = json_decode($result, true);

		return $jobs;
	}

	function outputJoblist() {
		$jobs=getJobs();
		$s="";

		if (count($jobs)==0) {
			$s = "No federal job opportunities are currently available on <a href='https://www.usajobs.gov/'>USAjobs.gov</a>.<BR>";
		} else {
			$jobSearchLink='https://www.usajobs.gov/Search?keyword=Broadcasting+Board+of+Governors&amp;Location=&amp;AutoCompleteSelected=&amp;search=Search';
			$s = "<p class='bbg__article-sidebar__tagline'>Includes job postings from the International Broadcasting Bureau, Voice of America and Office of Cuban Broadcasting.</p>";
			for ($i=0; $i < count($jobs); $i++) {
				$j=$jobs[$i];
				//var_dump($j);
				$url = $j['url'];
				$title=$j['position_title'];
				$startDate=$j['start_date'];
				$endDate=$j['end_date'];

				$locations=$j['locations'];

				$s.= "<p><a href='$url'>$title</a><br/>";
				$locationStr = "Location";
				if (count($locations)>1){
					$locationStr = "Locations";
				}

				$s.= $locationStr.": ";
				for ($k=0; $k<count($locations); $k++) {
					$loc = $locations[$k];
					$s.= "$loc<br/>";
				}
				$s .= "Closes: $endDate<br/>";
				$s .= "</p>";
			}
			$s .= "<p class='bbg__article-sidebar__tagline'>All federal job opportunities are available on <a target='_blank' href='$jobSearchLink'>USAjobs.gov</a></p>";
		}
		return $s;
	}

	// Add shortcode to output the jobs list
	function jobs_shortcode() {
		return outputJoblist();
	}
	add_shortcode('jobslist', 'jobs_shortcode');
	
	function outputEmployeeProfiles($type) {
		$qParams=array(
			'post_type' => array('post')
			,'post_status' => array('publish')
			,'posts_per_page' => 6
			,'cat' => get_cat_id('Employee'),
		);
		$custom_query = new WP_Query($qParams);

		$epStr = '<section class="usa-section">';
		$epStr .= '<h5 class="bbg-label small">Employees</h5>';
		$epStr .= '<p class="" style="font-family: sans-serif;">This is a description that goes here and here.</p>';
		$epStr .= '<div class="usa-grid-full">';
		while ( $custom_query->have_posts() )  {
			$custom_query->the_post();
			$id=get_the_ID();
			$active=get_post_meta( $id, 'active', true );
			$e = "";
			if ($active){
				$occupation=get_post_meta( $id, 'occupation', true );
				$twitterProfileHandle=get_post_meta( $id, 'twitter_handle', true );
				$profilePhotoID=get_post_meta( $id, 'profile_photo', true );
				$profilePhoto = "";
				if ($profilePhotoID) {
					$profilePhoto = wp_get_attachment_image_src( $profilePhotoID , 'mugshot');
					$profilePhoto = $profilePhoto[0];
				}
				$firstName=get_post_meta( $id, 'first_name', true );
				$lastName=get_post_meta( $id, 'last_name', true );
				$profileName = $firstName . " " . $lastName;
				$permalink=get_the_permalink();
				$e = '';
				$e .= '<div class="bbg__employee-profile__excerpt">';
				
				$e .= '<a href="'.$permalink.'"><img src="'.$profilePhoto.'"/></a>';
				$e .= '<h4><a href="'.$permalink.'">'.$profileName.'</h4>';
				$e .= '<h6>'.$occupation.'</h6>';
				$e .= '</div>';
				$epStr .= $e;
			}
		}

		$epStr .= '</div>';
		$epStr .= '</section>';
		return $epStr;
	}

	function employee_profile_list_shortcode() {
		return outputEmployeeProfiles();
	}
	add_shortcode('employee_profile_list', 'employee_profile_list_shortcode');




?>