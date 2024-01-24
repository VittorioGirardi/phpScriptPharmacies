<?php


if (date('N') == 3) { 

    // Set the default timezone to Italy (you may adjust this based on your needs)
    date_default_timezone_set('Europe/Rome');
    
    // Function to filter elements based on the current date (Wednesday)
    function filterElementsByCurrentDate($data, $timezone) {
        $filteredData = array();
    
        // Get the current date
        $currentDate = new DateTime('now', new DateTimeZone($timezone));
    
        foreach ($data as $row) {
            // Assuming the date is in the first column of each row
            $rowDate = $row[0];
    
            // Convert date strings to DateTime objects for easier comparison
            $rowDateTime = DateTime::createFromFormat('d/m/Y', $rowDate, new DateTimeZone($timezone));
    
            // Check if the row's date is the current Wednesday
            if ($rowDateTime && $currentDate->format('N') == 3 && $rowDateTime->format('Y-m-d') == $currentDate->format('Y-m-d')) {
                $filteredData[] = $row;
            }
        }
    
        return $filteredData;
    }
    
    // Read the CSV file
    $csvFile = './farmacia.csv'; // Update this with the correct path
    $file = fopen($csvFile, 'r');
    
    if ($file) {
        $data = array();
    
        while (($row = fgetcsv($file)) !== false) {
            $data[] = $row;
        }
    
        fclose($file);
    
        // Specify the Italy timezone
        $timezone = 'Europe/Rome';
    
        // Filter the data based on the current date (Wednesday)
        $filteredData = filterElementsByCurrentDate($data, $timezone);
    
        // Generate a temporary text file with the data
        $tempFileName = tempnam(sys_get_temp_dir(), 'data');
        $tempFile = fopen($tempFileName, 'w');
    
        foreach ($filteredData as $row) {
            fwrite($tempFile, implode(', ', $row) . PHP_EOL);
        }
    
        fclose($tempFile);
    
        // Define the output image path
        $outputImagePath = '/var/www/html/layout/1.png'; // Update this with the desired path
    
        // Execute ImageMagick command to convert text to an image
        $imageMagickCommand = "convert -background white -fill black -font Arial.ttf -size 800x600 caption:@$tempFileName $outputImagePath";
        exec($imageMagickCommand);
    
        // Output the image as PNG
        header('Content-Type: image/png');
        readfile($outputImagePath);
    
        // Clean up temporary files
        unlink($tempFileName);
    } else {
        echo 'Error opening the file.';
    }
    
    // Example: Print a message when running on Wednesday
    echo "Today is Wednesday! Processing CSV data...\n";


}