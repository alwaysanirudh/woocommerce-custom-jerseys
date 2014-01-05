<?php
extract($array_data_products);

$data_team_meta = array();
$data_team_meta_back_logos = array();
if ($is_search_design == true) {
  foreach ($dbl_team_meta as $dbr_team_meta) {
    if ($dbr_team_meta->team_meta_tab != 'back_logos') {
      continue;
    }

    $data_team_meta = json_decode($dbr_team_meta->team_meta_value, true);
  }

  foreach ($data_team_meta as $team_meta) {
    $data_team_meta_back_logos[$team_meta['position_id']] = $team_meta;
  }
}
?>
<article data-type="back" id="tab_back_logos">
  <a href="javascript:mostrarPestanhaDesbloqueada(2);" class="activo">
    < Prev
  </a>
  <header>
    Select Logos for the Back of the Jersey
  </header>
  <hgroup id="tab_back_logos_hgroup">
    <?php $i = 0; ?>
    <?php foreach ($positions as $key => $position): ?>
      <?php if ($position->type == 'BACK' && $position->for_logo_position == 1): ?>
        <?php $data_logos_default = array('position_id' => $position->id); ?>
        <?php
        $logo_current_class = '';
        $logo_current_name = 'Select of Upload a file';
        $data_back_logos = $data_logos_default;
        if ($is_search_design == true && isset($data_team_meta_back_logos[$position->id])) {

          $data_back_logos = $data_team_meta_back_logos[$position->id];
          $logo_current_class = 'seleccionado';
          $logo_current_name = $data_back_logos['name'];

          if (!isset($data_back_logos['sponsor_id'])) {
            $data_back_logos['filename'] = base64_encode($logo_current_name);
            unset($data_back_logos['name']);
          }
        }
        ?>
        <div class="<?php echo $logo_current_class; ?>" data-logos='<?php echo json_encode($data_back_logos); ?>'>
          <a href="javascript:elegirLogo(3,<?php echo $i; ?>);">
            <h1 data-content="name"><?php echo $position->name; ?></h1>
            <p><?php echo $logo_current_name; ?></p>
          </a>
          <a href="javascript:deselegirLogo(3,<?php echo $i; ?>);">x</a>
        </div>
        <?php $i++; ?>
      <?php endif; ?>
    <?php endforeach; ?>
  </hgroup>
  <a href="javascript:DesbloquearYmostrarPestanha(4);" data-btn="btn_next" class="activo">
    Next >
  </a>
</article>