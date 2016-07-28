<?php

	//$url = "http://events.ucf.edu/this-week/feed.xml";
	$url = "http://events.ucf.edu/this-month/feed.xml";
	$xml = simplexml_load_file($url); // XML parser

	foreach($xml->event as $event) {
	    print 'Title: '.$event->title.'<br />';
	    print 'Start Date: '.$event->start_date.'</br>';
	    print 'End Date: '.$event->end_date.'</br>';
	    print 'Location: '.$event->location.'</br>';
	    print 'Category: '.$event->category.'</br>';
	    print 'Contact Phone: '.$event->contact_phone.'</br>';
	    print 'Contact Email: '.$event->contact_email.'</br>';
	    print 'Description:<br>'.$event->description.'</br>';
	}

?>
