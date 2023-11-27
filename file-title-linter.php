<?php

$multiple_hyphen_pattern      = '/-{2,}/';
$hyphen_next_to_point_pattern = '/-\.|-\./';
$folder                       = $argv[1] ?? null;
$text_next_to_number_pattern  = '/((?![x])[a-z]+)(\d+)/';
$image_size_pattern           = '/@?(\d+x\d+)\.([a-zA-Z]+)/';

/**
 * Get all the files in a folder
 *
 * @param string $folder
 * @return array
 */
function getFiles($folder) {
  $files = scandir($folder);
  $files = array_diff($files, ['.', '..']);
  return $files;
}

// Check if <$folder> variable is set and exit the script if not
if (!isset($folder)) {
  exit("Please provide a folder name\nTry: php file-title-linter.php \e[1m<folder-name>\e[0m\n");
}

// Check if <$folder> variable is a valid folder and exit the script if not
if (!is_dir($folder)) {
  exit("Folder \e[1m<$folder>\e[0m is not a valid a folder or does not exist");
}

// Get all files in the folder
$files = getFiles($folder);

// For each file in the folder
foreach ($files as $file) {
  // Avoid prosessing folders
  if (is_dir($file)) {
    continue;
  }

  // Avoid processing the current directory (.) and the parent directory (..)
  if ($file === "." || $file === "..") {
    continue;
  }

  echo "Processing file \e[1m<$file>\e[0m\n";

  // Get the file name with the folder path
  $base_file_name = $folder . DIRECTORY_SEPARATOR . strtolower($file);

  if ($file[0] === "_") {
    $file = str_replace("_", "", $file);
  }

  // Keep track of the modifications done for the final file name
  $modified_file_name = $folder . DIRECTORY_SEPARATOR . strtolower($file);

  // Replace all the characters that are not allowed in a file name
  $new_file_name = str_replace(
    [" ", "_", ",","+", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "é", "è", "ê", "ë", "à", "ç", "œ", "ù", "ü", "ô", "ö", "ä", "â", "'", "copie", "web", "ß", "ä"],
    ["-", "-", "-", "-", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "e", "e", "e", "e", "a", "c", "oe", "u", "u", "o", "o", "a", "a", "-", "", "", "ss", "a"],
    $modified_file_name
  );

  // If the file name contains a number next to a text, add a hyphen between them
  if(preg_match($text_next_to_number_pattern, $new_file_name, $matches)){
    $new_file_name = preg_replace($text_next_to_number_pattern, "$1-$2", $new_file_name);
  }

  // If the file name contains an image size, remove it
  if (count($result_regex = preg_split($image_size_pattern, $new_file_name, -1, PREG_SPLIT_DELIM_CAPTURE)) > 1) {
    $new_file_name = $result_regex[0] . "." . $result_regex[2];

    // If the file name is already well formatted, skip it
    if ($modified_file_name === $new_file_name) {
      echo "File \e[1m<$base_file_name>\e[0m is already well formatted\n";
      continue;
    }

    // If the file name contains multiple hyphens, replace them with a single hyphen
    if (preg_match($multiple_hyphen_pattern, $new_file_name)) {
      $new_file_name = preg_replace($multiple_hyphen_pattern, "-", $new_file_name);
    }

    // If the file name contains a hyphen next to a point, replace it with a point
    if (preg_match($hyphen_next_to_point_pattern, $new_file_name)) {
      $new_file_name = preg_replace($hyphen_next_to_point_pattern, ".", $new_file_name);
    }

    // Rename the file
    if (rename($base_file_name, $new_file_name))
      echo "File \e[1m<$base_file_name>\e[0m renamed successfully to \e[1m<$new_file_name>\e[0m\n";
  } else {

    // If the file name contains multiple hyphens, replace them with a single hyphen
    if (preg_match($multiple_hyphen_pattern, $new_file_name)) {
      $new_file_name = preg_replace($multiple_hyphen_pattern, "-", $new_file_name);
    }

    // If the file name is already well formatted, skip it
    if ($base_file_name === $new_file_name) {
      echo "File \e[1m<$base_file_name>\e[0m is already well formatted\n";
      continue;
    }

    // If the file name contains a hyphen next to a point, replace it with a point
    if (preg_match($hyphen_next_to_point_pattern, $new_file_name)) {
      $new_file_name = preg_replace($hyphen_next_to_point_pattern, ".", $new_file_name);
    }

    // Rename the file
    rename($base_file_name, $new_file_name);
    echo "File \e[1m<$base_file_name>\e[0m renamed successfully to \e[1m<$new_file_name>\e[0m\n";
  }
}
