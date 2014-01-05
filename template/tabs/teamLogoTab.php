<?php
extract($array_data_products);

$data_team_meta_text_fonts = array();

if ($is_search_design == true) {
  foreach ($dbl_team_meta as $team_meta) {
    if ($team_meta->team_meta_tab != 'text_fonts') {
      continue;
    }

    $data_team_meta_text_fonts[$team_meta->team_meta_key] = $team_meta->team_meta_value;
  }
}
?>

<article id="tab_text_font">
  <a href="javascript:mostrarPestanhaDesbloqueada(0);" class="activo">
    < Prev
  </a>
  <div>
    <header>
      Team name or Logo
    </header>
    <?php foreach ($positions as $key => $position): ?>
      <?php if ($position->type == 'FRONT' && $position->name == 'TEAM_NAME/LOGO' && $position->for_logo_position == 0): ?>

        <?php
        $name = '';
        $type = '';
        $data_logos_default = array('position_id' => $position->id);
        $data_logos = array();

        if ($is_search_design) {
          $team_name_logo_value = json_decode($data_team_meta_text_fonts['_team_name_logo'], true);
          $name = $team_name_logo_value['name'];
          $type = $team_name_logo_value['type'];
          $data_logos['position_id'] = $position->id;

          if ($type == 'name') {
            $data_logos['filename'] = $name;
            $data_logos['type'] = $type;
          }

          if ($type == 'logo') {
            if (isset($team_name_logo_value['sponsor_id'])) {
              $data_logos['sponsor_id'] = $team_name_logo_value['sponsor_id'];
              $data_logos['filename'] = $team_name_logo_value['filename'];
              $data_logos['name'] = $team_name_logo_value['name'];
            } else {
              $data_logos['filename'] = base64_encode($name);
            }
            $data_logos['type'] = $type;
          }
        }
        ?>
        <?php ?>
        <input value="<?php echo ($type == 'name' ? $name : ''); ?>" data-logos='<?php echo json_encode(($type == 'name' ? $data_logos : $data_logos_default)); ?>' id="jdt_team_name" name="jdt[team_name]" type="text" placeholder="Write your team name" oninput="javascript:aumentarValidacionGrupo(0,0);"/>
        <h2>or</h2>
        <input value="<?php echo ($type == 'logo' ? $name : ''); ?>" data-logos='<?php echo json_encode(($type == 'logo' ? $data_logos : $data_logos_default)); ?>' id="jdt_logo_name" name="jdt[logo_name]" type="text" disabled="disabled" placeholder="Select or Upload a file" onchange="javascript:desbloquearNext(1);"/>
        <a href="javascript:elegirLogoTeam();" class="">Select</a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  <div>
    <header>Team name/Logo Position</header>
    <hgroup>
      <select id="jdt_logo_position_back" name="jdt[logo_position_back]" onchange="javascript:aumentarValidacionGrupo(1, 0);">
        <option value="">Back</option>
        <?php foreach ($positions as $key => $position): ?>
          <?php if ($position->type == 'BACK' && $position->for_logo_position == 1): ?>
            <?php
            $selected = '';
            if ($is_search_design && $data_team_meta_text_fonts['_team_position_back'] == $position->id) {
              $selected = 'selected="selected"';
            }
            ?>
            <option <?php echo $selected; ?>  value="<?php echo $position->id; ?>"><?php echo $position->name; ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <select id="jdt_logo_position_front" name="jdt[logo_position_front]" onchange="javascript:aumentarValidacionGrupo(1, 1);">       
        <option value="">Front</option>
        <?php foreach ($positions as $key => $position): ?>
          <?php if ($position->type == 'FRONT' && $position->for_logo_position == 1): ?>

            <?php
            $selected = '';
            if ($is_search_design && $data_team_meta_text_fonts['_team_position_front'] == $position->id) {
              $selected = 'selected="selected"';
            }
            ?>
            <option <?php echo $selected; ?>  value="<?php echo $position->id; ?>"><?php echo $position->name; ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <select id="jdt_logo_position_sleeve" name="jdt[logo_position_sleeve]" onchange="javascript:aumentarValidacionGrupo(1, 2);">
        <option value="">Sleeve</option>
        <?php foreach ($positions as $key => $position): ?>
          <?php if ($position->type == 'SLEEVE' && $position->for_logo_position == 1): ?>
            <?php
            $selected = '';
            if ($is_search_design && $data_team_meta_text_fonts['_team_position_sleeve'] == $position->id) {
              $selected = 'selected="selected"';
            }
            ?>
            <option <?php echo $selected; ?> value="<?php echo $position->id; ?>"><?php echo $position->name; ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
    </hgroup>    
    <?php
    $comment_logo = '';
    if ($is_search_design == true && isset($data_team_meta_text_fonts['_team_position_comment'])) {
      $comment_logo = $data_team_meta_text_fonts['_team_position_comment'];
    }
    ?>
    <textarea name="jdt[comment_logo]" id="jdt_comment_logo"  placeholder="Notes" ><?php echo $comment_logo; ?></textarea>
  </div>
  <div>
    <header>
      Fonts & Text Colors
    </header>
    <select name="jdt[font]" id="jdt_font" onchange="javascript:aumentarValidacionGrupo(2, 0);">
      <option value="">Font Set</option>
      <?php foreach ($font_colors as $font_color): ?>
        <?php if ($font_color->type == 'FONT'): ?>
          <?php
          $selected = '';
          if ($is_search_design && $data_team_meta_text_fonts['_team_font'] == $font_color->id) {
            $selected = 'selected="selected"';
          }
          ?>
          <option <?php echo $selected; ?> value="<?php echo $font_color->id; ?>"><?php echo $font_color->name; ?></option>      
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
    <select name="jdt[text_fill]" id="jdt_text_fill" onchange="javascript:aumentarValidacionGrupo(2, 1);">
      <option value="">Text Fill Color Set</option>
      <?php foreach ($font_colors as $font_color): ?>
        <?php if ($font_color->type == 'TEXT_FILL'): ?>
          <?php
          $selected = '';
          if ($is_search_design && $data_team_meta_text_fonts['_team_text_fill'] == $font_color->id) {
            $selected = 'selected="selected"';
          }
          ?>
          <option <?php echo $selected; ?> value="<?php echo $font_color->id; ?>"><?php echo $font_color->name; ?></option>      
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
    <select name="jdt[outline_color]" id="jdt_outline_color" onchange="javascript:aumentarValidacionGrupo(2, 2);">
      <option value="">Outline Color Set</option>
      <?php foreach ($font_colors as $font_color): ?>
        <?php if ($font_color->type == 'OUTLINE'): ?>
          <?php
          $selected = '';
          if ($is_search_design && $data_team_meta_text_fonts['_team_outline'] == $font_color->id) {
            $selected = 'selected="selected"';
          }
          ?>
          <option <?php echo $selected; ?> value="<?php echo $font_color->id; ?>"><?php echo $font_color->name; ?></option>      
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
  </div>
  <a data-btn="btn_next" href="javascript:DesbloquearYmostrarPestanha(2);">
    Next >
  </a>
</article>