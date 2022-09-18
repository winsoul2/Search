<?php

    class Pagination_center
    {
        function paginating($page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20, $get_value = ''){
            $pages = ((int) ceil($row_total / $per_page));
            $max = min($pages, $page_limit);
            $limit = ((int) floor($max / 2));
            $leading = $limit;
            for ($x = 0; $x < $limit; ++$x) {
                if ($page_now === ($x + 1)) {
                    $leading = $x;
                    break;
                }
            }
            for ($x = $pages - $limit; $x < $pages; ++$x) {
                if ($page_now === ($x + 1)) {
                    $leading = $max - ($pages - $x);
                    break;
                }
            }
			$get_data = '';
			if(!empty($get_value)){
				foreach($get_value as $key => $value){
					if($key != 'page'){
						$get_data .= $key.'='.str_replace('"','',json_encode($value)).'&';
					}
				}
				$get_data = substr($get_data,0,-1);
			}
			
            $trailing = $max - $leading - 1;
            $paginating = '';
            $paginating .= '<ul class="clearfix pagination">';
            if($page_now == 1){
                $prev_class = 'disabled';
                $href = '#';
            }else{
                $prev_class = '';
                $href = '?page='.($page_now-1);
            }
            $paginating .= '<li class="copy previous '.$prev_class.'"><a href="'.$href.'&'.$get_data.'">&laquo; Previous</a></li>';
            for ($x = 0; $x < $leading; ++$x) {
                $x_page = ($page_now + $x - $leading);
                $paginating .= '<li class="number"><a data-pagenumber="'.$x_page.'" href="?page='.$x_page.'&'.$get_data.'">'.$x_page.'</a></li>';
            }
            $paginating .= '<li class="number active"><a data-pagenumber="'.$page_now.'" href="#">'.$page_now.'</a></li>';
            for ($x = 0; $x < $trailing; ++$x) {
                $x_page = ($page_now + $x + 1);
                $paginating .= '<li class="number"><a data-pagenumber="'.$x_page.'" href="?page='.$x_page.'&'.$get_data.'">'.$x_page.'</a></li>';
            }
            if(($page_now+1) > $pages){
                $next_class = 'disabled';
                $href = '#';
            }else{
                $next_class = '';
                $href = '?page='.($page_now+1);
            }
            $paginating .= '<li class="copy next '.$next_class.'"><a href="'.$href.'&'.$get_data.'">Next &raquo;</a></li>';
            $paginating .= '</ul>';
            return $paginating;
        }

        function paginating_with_name($page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20, $get_value = '', $name_arr){
            $pages = ((int) ceil($row_total / $per_page));
            $max = min($pages, $page_limit);
            $limit = ((int) floor($max / 2));
            $leading = $limit;
            for ($x = 0; $x < $limit; ++$x) {
                if ($page_now === ($x + 1)) {
                    $leading = $x;
                    break;
                }
            }
            for ($x = $pages - $limit; $x < $pages; ++$x) {
                if ($page_now === ($x + 1)) {
                    $leading = $max - ($pages - $x);
                    break;
                }
            }
            $get_data = '';
            if(!empty($get_value)){
                foreach($get_value as $key => $value){
                    if($key != 'page'){
                        $get_data .= $key.'='.str_replace('"','',json_encode($value)).'&';
                    }
                }
                $get_data = substr($get_data,0,-1);
            }

            $trailing = $max - $leading;
            $paginating = '';
            $paginating .= '<ul class="clearfix pagination">';
            if($page_now == 1){
                $prev_class = 'disabled';
                $href = '#';
            }else{
                $prev_class = '';
                $href = '?page='.($page_now-1);
            }
            $paginating .= '<li class="copy previous '.$prev_class.'"><a href="'.$href.'&'.$get_data.'">&laquo; Previous</a></li>';
            for ($x = 0; $x < $leading; ++$x) {
                $x_page = ($page_now + $x - $leading);
                $page_name = $name_arr[$x_page - 1];
                $paginating .= '<li class="number"><a data-pagenumber="'.$x_page.'" href="?page='.$x_page.'&'.$get_data.'">'.$page_name.'</a></li>';
            }
            $paginating .= '<li class="number active"><a data-pagenumber="'.$page_now.'" href="#">'.$name_arr[$page_now-1].'</a></li>';
            for ($j = 1; $j < $trailing; ++$j) {
                $x_page = ($page_now + $j);
                $page_name = $name_arr[$x_page - 1];
                $paginating .= '<li class="number"><a data-pagenumber="'.$x_page.'" href="?page='.$x_page.'&'.$get_data.'">'.$page_name.'</a></li>';
            }
            if(($page_now+1) > $pages){
                $next_class = 'disabled';
                $href = '#';
            }else{
                $next_class = '';
                $href = '?page='.($page_now+1);
            }
            $paginating .= '<li class="copy next '.$next_class.'"><a href="'.$href.'&'.$get_data.'">Next &raquo;</a></li>';
            $paginating .= '</ul>';
            return $paginating;
        }
    }
