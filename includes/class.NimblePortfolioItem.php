<?php

if (!class_exists('NimblePortfolioItem')) {

    class NimblePortfolioItem {

        private $post;
        private $id;

        public function __construct($id = null, $params = array()) {

            if ($id === null) {
                return;
            }
            $this->id = $id;
            $this->post = get_post($id);
        }

        public function __get($name) {
            return $this->post->$name;
        }

        public function __set($name, $value) {
            return;
        }

        function getData($field) {
            $custom_field = get_post_meta($this->id, $field, true);

            $custom_field = apply_filters('nimble_portfolio_get_field', $custom_field, $field);

            return $custom_field;
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

            if (empty($meta['sizes']) || empty($meta['sizes'][$size_name])) {

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
                return $resized_path;

                // perhaps this image already exists.  If so, return it.
            } else {
                $orig_info = pathinfo($original_path);
                $suffix = "{$width}x{$height}";
                $dir = $orig_info['dirname'];
                $ext = $orig_info['extension'];
                $name = basename($original_path, ".{$ext}");
                $destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";
                if (file_exists($destfilename)) {
                    return $destfilename;
                }
            }

            return '';
        }

        function getThumbnail($size_name, $crop = true) {
            $src = $this->getAttachmentSrc(get_post_thumbnail_id($this->id), $size_name, $crop);
            return $src[0];
        }

        function getFilters($taxonomy, $format = 'A') {

            $_terms = wp_get_post_terms($this->id, $taxonomy);

            if ($format == 'R') {
                return $_terms;
            }

            $filters = array();
            foreach ($_terms as $_term) {
                $filters[] = $_term->slug;
            }

            if ($format == 'S') {
                return implode(" ", $filters);
            }

            return $filters;
        }

    }

}