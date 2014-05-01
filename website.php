<?php
#Offsets of graphs from sid of the window, need to keep track of for accurate graphing purposes
$offset_in_x = 350;
$offset_in_y = 50;
$length_of_x = 720;
$length_of_y = 720;
$scaling_factor_y = 18;

#Grabbing the stored temp and time info from the database 
$db = new SQLite3('/home/pi/project2/temp.db');
$temp = $db->query('SELECT temp FROM templog DESC LIMIT 1440');
$min = $db->query('SELECT strftime("%M", time(date)) FROM templog DESC LIMIT 1440');
$hour = $db->query('SELECT strftime("%H", time(date)) FROM templog DESC LIMIT 1440');

#count number of rows, useful for iteration
$num = $db->query("SELECT COUNT(*) as count FROM templog DESC LIMIT 1440");
$row = $num->fetchArray();
$num_rows = $row['count'];


$x=$offset_in_x;
$i=0;

#heres that usefulness i was talking about earlier
while(($dataRow= $temp->fetchArray()) && ($Hour = $hour->fetchArray()) && ($Min = $min->fetchArray())) {
        //Calculation for the y-coordinates
        $y = $dataRow[0];
        //Calculation for the x-coorinates
        $hourx = intval($Hour[0]);
        $minx = intval($Min[0]);
        $x =350+(30*($hourx + $minx*(1/60)));

        //Putting the points into x and y separate arrays
        $points[$i][0] = $x;
        $points[$i][1] = $y;

        $i++;
}

#This creates the first background 
Header( "Content-type: image/gif");
$gif = imagecreate($offset_in_x+$length_of_x,$offset_in_y+$length_of_y+50);

#Define colors for later use in graphing, quite obviously named 
$purple = ImageColorAllocate($gif, 153, 0, 153);
$magenta = imagecolorallocate($gif, 255, 0, 255);
$blue = imagecolorallocate($gif, 0, 0, 255);
$silver = imageColorAllocate($gif, 204, 204, 204); 
$black = imagecolorallocate($gif, 0, 0, 0);

#Use sky blue to give title space
ImageFilledRectangle($gif, $offset_in_x, 0, $offset_in_x+$length_of_x, $offset_in_y, $magenta);

#Same as above only Y axis
ImageFilledRectangle($gif, 0, 0, $offset_in_x, $offset_in_y+$length_of_y, $magenta);

#Same as above except for x axis
ImageFilledRectangle($gif, 0, $offset_in_y+$length_of_y, $offset_in_x+$length_of_x, $offset_in_y + $length_of_y+50, $white);

#Filling in the backdrop of the graph
ImageFilledRectangle($gif, $offset_in_x, $offset_in_y, $offset_in_x + $length_of_x, $offset_in_y +$length_of_y, $silver);

imagesetthickness($gif, 8);

#Actually putting up the y axis
ImageLine($gif,350,50,350,770,$black);

#X axis is being created now
ImageLine($gif,350,768,1070,768,$black);
imagesetthickness($gif, 1);

#Below is drawing the grid on the graph as well as labelling the numbers 
#this is the y axis stuff
$yaxinc = 1;
for($k = 0; $k < 40; $k++){
        ImageLine($gif, $offset_in_x, $offset_in_y + (18*$k), $offset_in_x + $length_of_x, $offset_in_y + (18*$k), $black);
        ImageString($gif, 5, $offset_in_x-25, $offset_in_y+(18*$k), $yaxinc*(40-$k), $black);
}


#this is the x axis stuff
$xaxinc = 30;
for($j = 0; $j < 24; $j++){
        ImageString($gif, 5, $offset_in_x+($xaxinc*$j), $offset_in_y+$length_of_y+10, $j, $black);
}

#Putting titles on the graph so that the data is actually meaningful to someone looking at the graph
$ytle = "Temperature (C) ";
$xtle = "Time (Hours)";
$tle = "Temperature in Dan's Room";
ImageString($gif, 5, 600, 25, $tle, $blue);
ImageStringUp($gif,5, 307, 500, $ytle, $blue);
ImageString($gif, 5, 600, 795, $xtle, $blue);
imagesetthickness($gif, 3);
for($i=0;$i<$num_rows-1;$i++){
        $error = ImageLine($gif , $points[$i][0],(770-($points[$i][1]*$scaling_factor_y)),$points[$i+1][0],(770-($points[$i+1][1] * $scaling_factor_y)),$purple);
}


#This outputs the image which in this case is a gif to the screen and releases memory
imagegif($gif);

#This does what it sounds like, destroys the gif. 
imagedestroy($gif);


?>