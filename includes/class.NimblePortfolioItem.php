<?php

if (!class_exists('NimblePortfolioItem')) {

    class NimblePortfolioItem {

        private $post;
        private $id;
        private $params;

        public function __construct($id = null, $params = array()) {

            if ($id === null) {
                return;
            }
            $this->id = $id;
            $this->post = get_post($id);
            $this->params = $params;
            $settings = NimblePortfolioPlugin::getGlobalSettings();
            $this->params['force-nothumbcache'] = isset($settings['thumb_nocache']) ? $settings['thumb_nocache'] : false;
            $this->params['force-exactthumbsize'] = isset($settings['thumb_exact_size']) ? $settings['thumb_exact_size'] : false;
        }

        public function __get($name) {
            return $this->post->$name;
        }

        public function __set($name, $value) {
            return;
        }

        public function getPostObject() {
            return $this->post;
        }

        public function getParam($param) {
            return isset($this->params[$param]) ? $this->params[$param] : null;
        }

        public function setParam($param, $value) {
            $this->params[$param] = $value;
        }

        function getData($field) {
            $value = get_post_meta($this->id, $field, true);

            if ($field == 'nimble-portfolio' && !$value) {
                $value = $this->getThumbnail('full');
            }

            $value = apply_filters('nimble_portfolio_get_field', $value, $field, $this);

            return $value;
        }

        public function getTitle() {
            return $this->post->post_title;
        }

        public function getPermalink() {
            return get_permalink($this->id);
        }

        public function getType($itemSrc = null) {

            $itemSrc = $itemSrc ? $itemSrc : $this->getData('nimble-portfolio');

            if (preg_match('/youtube\.com\/watch/i', $itemSrc) || preg_match('/youtu\.be/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/vimeo\.com/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.mov\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.swf\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.avi\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.mpg\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.mpeg\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.mp4\b/i', $itemSrc)) {
                return 'video';
            } else if (preg_match('/\b.pdf\b/i', $itemSrc)) {
                return 'googledoc';
            } else {
                return 'image';
            }
        }

        function getAttachmentSrc($attachment_id, $size_name = 'thumbnail', $crop = true) {

            global $_wp_additional_image_sizes;
            $size_name = trim($size_name);
            $meta = wp_get_attachment_metadata($attachment_id);

            if ($this->getParam('force-nothumbcache') || empty($meta['sizes']) || empty($meta['sizes'][$size_name])) {

                // let's first see if this is a registered size
                if (isset($_wp_additional_image_sizes[$size_name])) {
                    $height = (int) $_wp_additional_image_sizes[$size_name]['height'];
                    $width = (int) $_wp_additional_image_sizes[$size_name]['width'];
                    $crop = (bool) $_wp_additional_image_sizes[$size_name]['crop'];

                    // if not, see if name is of form [width]x[height] and use that to crop
                } else if (preg_match('#^(\d+)x(\d+)$#', $size_name, $matches)) {
                    $height = (int) $matches[2];
                    $width = (int) $matches[1];
                }

                if (!empty($height) && !empty($width)) {
                    $resized_path = $this->generateAttachment($attachment_id, $width, $height, $crop);
                    $fullsize_url = wp_get_attachment_url($attachment_id);

                    $file_name = basename($resized_path);
                    $new_url = str_replace(basename($fullsize_url), $file_name, $fullsize_url);

                    if (!empty($resized_path)) {
                        $meta['sizes'][$size_name] = array(
                            'file' => $file_name,
                            'width' => $width,
                            'height' => $height,
                        );

                        wp_update_attachment_metadata($attachment_id, $meta);
                        return array(
                            $new_url,
                            $width,
                            $height
                        );
                    }
                }
            }
            return wp_get_attachment_image_src($attachment_id, $size_name);
        }

        function generateAttachment($attachment_id = 0, $width = 0, $height = 0, $crop = true) {
            $attachment_id = (int) $attachment_id;
            $width = (int) $width;
            $height = (int) $height;
            $crop = (bool) $crop;

            $original_path = get_attached_file($attachment_id);

            $resized_path = @image_resize($original_path, $width, $height, $crop);

            if (
                    !is_wp_error($resized_path) &&
                    !is_array($resized_path)
            ) {

                if ($this->getParam('force-exactthumbsize')) {
                    $this->makeExactSize($resized_path, $width, $height);
                }
                return $resized_path;
            } else {

                $orig_info = pathinfo($original_path);
                $suffix = "{$width}x{$height}";
                $dir = $orig_info['dirname'];
                $ext = $orig_info['extension'];
                $name = basename($original_path, ".{$ext}");
                $destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";
                if (file_exists($destfilename)) {

                    if ($this->getParam('force-exactthumbsize')) {
                        $this->makeExactSize($destfilename, $width, $height);
                    }
                    return $destfilename;
                } elseif (copy($original_path, $destfilename)) {

                    if ($this->getParam('force-exactthumbsize')) {
                        $this->makeExactSize($destfilename, $width, $height);
                    }
                    return $destfilename;
                }
            }

            return '';
        }

        function getThumbnail($size_name, $crop = true) {
            if ($attachment_id = get_post_thumbnail_id($this->id)) {
                $src = $this->getAttachmentSrc($attachment_id, $size_name, $crop);
                return $src[0];
            }
            return '';
        }

        function getFilters($taxonomy, $format = 'A', $sep = ' ') {

            if (!$taxonomy) {
                return array();
            }

            $_terms = wp_get_post_terms($this->id, $taxonomy);

            if ($format == 'R') {
                return $_terms;
            }

            if ($format == 'N') {
                $filters = array();
                foreach ($_terms as $_term) {
                    $filters[] = $_term->name;
                }
                return implode($sep, $filters);
            }

            $filters = array();
            foreach ($_terms as $_term) {
                $filters[] = $_term->slug;
            }

            if ($format == 'S') {
                return implode($sep, $filters);
            }

            return $filters;
        }

        function makeExactSize($filename, $output_w, $output_h) {

            if (!$output_w || !$output_h || $output_w != $output_h) {
                return;
            }

            list($orig_w, $orig_h) = getimagesize($filename);

            $orig_img = imagecreatefromstring(file_get_contents($filename));

            // determine scale based on the longest edge
            if ($orig_h > $orig_w) {
                $scale = $output_h / $orig_h;
            } else {
                $scale = $output_w / $orig_w;
            }

            // calc new image dimensions
            $new_w = $orig_w * $scale;
            $new_h = $orig_h * $scale;

            // determine offset coords so that new image is centered
            $offest_x = ($output_w - $new_w) / 2;
            $offest_y = ($output_h - $new_h) / 2;

            // create new image and fill with background colour
            $new_img = imagecreatetruecolor($output_w, $output_h);
            $bgcolor = imagecolorallocate($new_img, 255, 255, 255);
            imagefill($new_img, 0, 0, $bgcolor); // fill background colour
            // copy and resize original image into center of new image
            imagecopyresampled($new_img, $orig_img, $offest_x, $offest_y, 0, 0, $new_w, $new_h, $orig_w, $orig_h);

            //save it
            imagejpeg($new_img, $filename, 90);
        }

    }

}