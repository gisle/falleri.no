<?php

$posts = [];


# Load data from database
$db = new PDO('mysql:host=localhost;dbname=npl;charset=utf8mb4', 'gaa041', 'pass1');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

foreach ($db->query("SELECT id, post_author, post_title, post_date, post_modified, post_content, post_name, post_parent, post_type FROM npl_posts WHERE post_status != 'auto-draft' ORDER BY id") as $row) {
    $posts[$row['id']] = [
	"id" => $row['id'],
	"author" => $row['post_author'],
	"title" => $row['post_title'],
	"date" => $row['post_date'],
	"modified" => $row['post_modified'],
	"content" => $row['post_content'],
	"name" => $row['post_name'],
	"parent" => $row['post_parent'],
	"type" => $row['post_type'],
    ];
}

foreach ($posts as $id => $post) {
    if ($post['type'] == 'page' || $post['type'] == 'post') {
	$dir = "content/$post[type]s";
	$name = $post['name'];
	$name = str_replace("%c3%b8", "ø", $name);
	$name = str_replace("%c3%a6", "æ", $name);
	$name = str_replace("-%e2%80%93-", "-", $name);
	$file = "$dir/$name.md";
	@mkdir($dir, 0755);
	$fh = fopen($file, "w") or die("Unable to create $file");
	fwrite($fh, "---\n");
	fwrite($fh, "title: $post[title]\n");
	fwrite($fh, "date: $post[date]\n");
	fwrite($fh, "---\n\n");
	$content = str_replace("\r", "", $post['content']);
	$content = str_replace("http://www.aas-sw.no/npl3/wp-content", "", $content);
	$content = str_replace("http://falleri.no/wp-content", "", $content);
	$content = str_replace('<p style="margin-bottom: 0cm">&nbsp;</p>', "\n", $content);
	fwrite($fh, $content);
	fwrite($fh, "\n");
	fclose($fh);
    }
}
