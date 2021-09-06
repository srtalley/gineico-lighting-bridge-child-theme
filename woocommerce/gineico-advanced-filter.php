<?php
// get filter all options
$args = array(
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => false,
);
$areas               = get_terms('product_cat', array_merge($args, array('parent' => 0)));
$brands              = get_terms('brands', $args);
$ip_ratings          = get_terms('ip_rating', $args);
$surfaces            = get_terms('surface', $args);
$colours             = bridge_child_get_products_colours();
$colour_temperatures = get_terms('colour_temperature', $args);
$cris                = get_terms('cri', $args);
$formats             = get_terms('format', $args);
$maximum_depths      = get_terms('maximum_depth', $args);
$beam_angles         = get_terms('beam_angle', $args);
$control_protocols   = get_terms('control_protocol', $args);

// get filter selected values
$gineico_filter = isset($_GET['gineico-filter']) ? true : false;
$search_text = isset($_GET['gineico-s']) ? esc_attr($_GET['gineico-s']) : '';
$search_area = isset($_GET['area']) ? $_GET['area'] : array();
$search_area = array_map('esc_attr', $search_area);
$search_brand = isset($_GET['brand']) ? $_GET['brand'] : array();
$search_brand = array_map('esc_attr', $search_brand);
$search_ip_rating = isset($_GET['ip_rating']) ? esc_attr($_GET['ip_rating']) : '';
$search_surface = isset($_GET['surface']) ? esc_attr($_GET['surface']) : '';
$search_colour = isset($_GET['colour']) ? $_GET['colour'] : array();
$search_colour = array_map('esc_attr', $search_colour);
$search_colour_temperature = isset($_GET['colour_temperature']) ? $_GET['colour_temperature'] : array();
$search_colour_temperature = array_map('esc_attr', $search_colour_temperature);
$search_cri = isset($_GET['cri']) ? $_GET['cri'] : array();
$search_cri = array_map('esc_attr', $search_cri);
$search_format = isset($_GET['format']) ? esc_attr($_GET['format']) : '';
$search_maximum_depth = isset($_GET['maximum_depth']) ? esc_attr($_GET['maximum_depth']) : '';
$search_beam_angle = isset($_GET['beam_angle']) ? $_GET['beam_angle'] : array();
$search_beam_angle = array_map('esc_attr', $search_beam_angle);
$search_control_protocol = isset($_GET['control_protocol']) ? $_GET['control_protocol'] : array();
$search_control_protocol = array_map('esc_attr', $search_control_protocol);
?>

<div class="gineico-advanced-search <?php echo $gineico_filter ? "collapse" : ""; ?>">
	<form method="get" action="<?php echo esc_url( home_url( '/advanced-search/'  ) ); ?>">
		<input type="hidden" name="gineico-filter" value="1">
		<div class="main-filter">
			<input type="text" class="gineico-s" name="gineico-s" placeholder="<?php _e('Search Products (Name / SKU)', 'gineicolighting'); ?>" autocomplete="off" value="<?php echo $search_text; ?>">
			<button type="button" class="gineico-btn reset"><?php _e('Reset', 'gineicolighting'); ?></button>
			<button type="submit" class="gineico-btn search"><?php _e('Search', 'gineicolighting'); ?></button>
		</div>
		<div class="advanced-filter">
			<div class="col">
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Area', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($areas as $key => $area) {
								if($area->slug == 'uncategorized') {
									continue;
								}
								echo '<li><label><input type="checkbox" name="area[]" value="' . $area->slug . '" ' . (in_array($area->slug, $search_area) ? 'checked' : '') . '><span></span>' . $area->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Brand', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options">
							<?php
							foreach ($brands as $key => $brand) {
								echo '<li><label><input type="checkbox" name="brand[]" value="' . $brand->slug . '" ' . (in_array($brand->slug, $search_brand) ? 'checked' : '') . '><span></span>' . $brand->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Surface', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($surfaces as $key => $surface) {
								echo '<li><label><input type="radio" name="surface" value="' . $surface->slug . '" ' . checked($surface->slug, $search_surface, false) . '><span></span>' . $surface->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Colour', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options">
							<?php
							foreach ($colours as $colour) {
								echo '<li><label><input type="checkbox" name="colour[]" value="' . $colour . '" ' . (in_array($colour, $search_colour) ? 'checked' : '') . '><span></span>' . $colour . '</label></li>';
							}
							?>
						</ul>
						<?php /*
						<select class="colour-select-2" name="colour[]" multiple>
							<?php
							foreach ($colours as $colour) {
								echo '<option value="' . $colour . '" ' . (in_array($colour, $search_colour) ? 'selected' : '') . '>' . $colour . '</option>';
							}
							?>
						</select>
						*/ ?>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Colour Temperature', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($colour_temperatures as $key => $colour_temperature) {
								echo '<li><label><input type="checkbox" name="colour_temperature[]" value="' . $colour_temperature->slug . '" ' . (in_array($colour_temperature->slug, $search_colour_temperature) ? 'checked' : '') . '><span></span>' . $colour_temperature->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('IP Rating', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($ip_ratings as $key => $ip_rating) {
								echo '<li><label><input type="radio" name="ip_rating" value="' . $ip_rating->slug . '" ' . checked($ip_rating->slug, $search_ip_rating, false) . '><span></span>' . $ip_rating->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Maximum Depth', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options">
							<?php
							foreach ($maximum_depths as $key => $maximum_depth) {
								echo '<li><label><input type="radio" name="maximum_depth" value="' . $maximum_depth->slug . '" ' . checked($maximum_depth->slug, $search_maximum_depth, false) . '><span></span>' . $maximum_depth->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Beam Angle', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($beam_angles as $key => $beam_angle) {
								echo '<li><label><input type="checkbox" name="beam_angle[]" value="' . $beam_angle->slug . '" ' . (in_array($beam_angle->slug, $search_beam_angle) ? 'checked' : '') . '><span></span>' . $beam_angle->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Format', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options">
							<?php
							foreach ($formats as $key => $format) {
								echo '<li><label><input type="radio" name="format" value="' . $format->slug . '" ' . checked($format->slug, $search_format, false) . '><span></span>' . $format->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('Control Protocol', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options">
							<?php
							foreach ($control_protocols as $key => $control_protocol) {
								echo '<li><label><input type="checkbox" name="control_protocol[]" value="' . $control_protocol->slug . '" ' . (in_array($control_protocol->slug, $search_control_protocol) ? 'checked' : '') . '><span></span>' . $control_protocol->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="filter-group">
					<div class="filter-group-title">
						<?php _e('CRI', 'gineicolighting'); ?>
					</div>
					<div class="filter-group-body">
						<ul class="filter-options two-col">
							<?php
							foreach ($cris as $key => $cri) {
								echo '<li><label><input type="checkbox" name="cri[]" value="' . $cri->slug . '" ' . (in_array($cri->slug, $search_cri) ? 'checked' : '') . '><span></span>' . $cri->name . '</label></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
