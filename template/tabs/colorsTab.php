<?php
extract($array_data_products);
?>
<article id="tab_colors" class="visible">
  <header>
    Select a color combination
  </header>
  <hgroup>
    <?php $data_form['product_id'] = $product_id; ?>    
    <?php $i = 0; ?>
    <?php foreach ($colors as $key => $color): ?>
      <?php $data_form['color_id'] = $color->id; ?>
      <?php
      if ($key == 0) {
        continue;
      }
      ?>
      <?php $color_class_current = ''; ?>
      <?php if ($is_search_design == true): ?>
        <?php if ($dbr_team->color_id == $color->id): ?> 
          <?php $color_class_current = 'seleccionado'; ?>
        <?php endif; ?>
      <?php endif; ?>
      <a class="<?php echo $color_class_current; ?>" data-images='<?php echo json_encode($color); ?>' data-form_data='<?php echo json_encode($data_form); ?>' href="javascript:elegirColor(<?php echo $i++ ?>);">      
        <div style="background-color: <?php echo $color->color_left; ?>"></div>
        <div style="background-color: <?php echo $color->color_right; ?>"></div>
      </a>
    <?php endforeach; ?>
  </hgroup>
  <a data-btn="btn_next" href="javascript:DesbloquearYmostrarPestanha(1);">
    Next >
  </a>

</article>