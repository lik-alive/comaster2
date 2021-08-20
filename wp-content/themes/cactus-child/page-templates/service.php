<?php
/*
		Template Name: Service
	*/
get_header();


$folder = ABSPATH . 'files/articles/';

$arr1 = glob($folder . '*');
foreach ($arr1 as $v1) {
	if (is_file($v1)) echo "Wrong file $v1 <br/>";
	else {
		$ID_Article = basename($v1);
		$article = dbhandler_get_entity('wp_ab_articles', 'ID_Article', $ID_Article);
		if (null === $article) echo "Article does not exist $ID_Article <br/>";

		$arr2 = glob($v1 . '/*');
		foreach ($arr2 as $v2) {
			$type = basename($v2);
			if ('reviews' === $type) {
				$arr3 = glob($v2 . '/*');
				foreach ($arr3 as $v3) {
					$ID_Review = basename($v3);
					$review = dbhandler_get_entity('wp_ab_reviews', 'ID_Review', $ID_Review);
					if (null === $review) echo "Review does not exist $ID_Article-$ID_Review <br/>";

					$arr4 = glob($v3 . '/*');
					if (0 === sizeof($arr4)) echo "Empty folder $ID_Article-$ID_Review <br/>";
				}
			} else if ('versions' === $type) {
				$arr3 = glob($v2 . '/*');
				foreach ($arr3 as $v3) {
					$ID_Version = basename($v3);
					$version = dbhandler_get_entity('wp_ab_versions', 'ID_Version', $ID_Version);
					if (null === $version) echo "Version does not exist $ID_Article-$ID_Version <br/>";

					$arr4 = glob($v3 . '/*');
					if (0 === sizeof($arr4)) echo "Empty folder $ID_Article-$ID_Version <br/>";
				}
			} else {
				echo "Wrong file/folder $v2 <br/>";
			}
		}
	}
}
?>



<?php get_footer(); ?>
