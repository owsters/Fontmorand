<?php
/**
 * Expects before include:
 *   $pageTitle       string
 *   $pageDescription string
 *   $baseUrl         string  '' at site root, '../' from web/pages/*
 */
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
<meta name="author" content="Owen Daniels">
<link rel="shortcut icon" href="<?php echo $baseUrl; ?>img/favicon.png">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link href="<?php echo $baseUrl; ?>css/site.css" rel="stylesheet">
</head>
<body>
