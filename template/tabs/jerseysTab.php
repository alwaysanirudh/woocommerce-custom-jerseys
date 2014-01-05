<?php
extract($array_data_products);
?>

<article id="tab_jerseys" class="">
  <a href="javascript:mostrarPestanhaDesbloqueada(4);" class="activo">
    < Prev
  </a>
  <header>
    Jerseys
  </header>
  <div id="wrapper_jerseys">
    <hgroup id="newJersey">
      <div>
        <a href="javascript:mostrarNewJersey('newJersey');">
          <p>
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 90 90"  xml:space="preserve">
              <rect id="rectangle" stroke="none" fill="rgb(0, 0, 0)" x="45" y="0" width="2" height="90" />
              <path id="rectangle2" stroke="none" fill="rgb(0, 0, 0)" d="M 90,45 L 90,43 0,43 0,45 90,45 Z M 90,45" />
            </svg>
          </p>
        </a>
      </div>
      <div>
        <input name="name" type="text" placeholder="Name" />
        <input name="number" type="text" placeholder="Number" />
        <select name="size">
          <option value="" selected="selected">Size</option>
          <?php foreach ($size_values as $key => $size) : ?>
            <option value="<?php echo $size->slug; ?>"><?php echo $size->name; ?></option>
          <?php endforeach; ?>
        </select>
        <a href="javascript:agregarNuevoJersey();">Add</a>
        <a href="javascript:ocultarNewJersey('newJersey')">Cancel</a>
      </div>
    </hgroup>
    <hgroup data-template="" id="jersey{{indice}}">
      <div>
        <h1>{{indice}}</h1>
        <h2>Name:</h2>
        <span>{{name}}</span>
        <h2>Number:</h2>
        <span>{{number}}</span>
        <h2>Size:</h2>
        <span>{{size}}</span>
        <a href="javascript:mostrarNewJersey('jersey{{indice}}');" class="editarJersey">
          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 20 20"  xml:space="preserve">
            <path id="bezier" stroke="none" fill="rgb(89, 101, 102)" d="M 16.18,1.3 L 18.66,3.77 C 19.11,4.22 19.11,4.95 18.66,5.4 18.22,5.85 17.49,5.85 17.04,5.4 L 14.6,2.96 C 14.15,2.51 14.15,1.78 14.6,1.34 15.05,0.89 15.78,0.89 16.23,1.34 L 16.18,1.3 Z M 16.23,7.83 L 7.29,16.77 3.23,12.71 12.17,3.77 16.23,7.83 Z M 6.16,18.06 L 1,19 1.94,13.84 6.16,18.06 Z M 6.16,18.06" />
          </svg>
        </a>
        <a href="javascript:eliminarJersey('jersey{{indice}}', {{indice}} );"  class="eliminarJersey">
          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 59 59"  xml:space="preserve">
            <g id="group">
              <path id="roundedRectangle6" stroke="none" fill="rgb(29, 180, 255)" d="M 12.93,50.17 C 12.93,51.57 14.07,52.7 15.47,52.7 L 43.52,52.7 C 44.93,52.7 46.08,51.57 46.08,50.17 L 46.08,12.35 48.62,14.87 10.38,14.87 12.93,12.35 12.93,50.17 Z M 7.82,50.17 L 7.82,12.35 7.82,9.83 10.38,9.83 48.62,9.83 51.17,9.83 51.17,12.35 51.17,50.17 C 51.17,54.35 47.75,57.74 43.52,57.74 L 15.47,57.74 C 11.25,57.74 7.82,54.35 7.82,50.17 Z M 7.82,50.17" />
              <rect id="rectangle49" stroke="none" fill="rgb(29, 180, 255)" x="18" y="25" width="5" height="21" />
              <rect id="rectangle50" stroke="none" fill="rgb(29, 180, 255)" x="27" y="25" width="5" height="21" />
              <rect id="rectangle51" stroke="none" fill="rgb(29, 180, 255)" x="36" y="25" width="5" height="21" />
              <path id="bezier3" stroke="none" fill="rgb(29, 180, 255)" d="M 46.08,11.09 L 46.08,12.35 46.08,14.87 43.52,14.87 15.47,14.87 12.93,14.87 12.93,12.35 12.93,11.09 C 12.93,5.52 17.49,1 23.12,1 L 35.88,1 C 41.51,1 46.08,5.52 46.08,11.09 Z M 40.98,11.09 C 40.98,8.3 38.69,6.04 35.88,6.04 L 23.12,6.04 C 20.31,6.04 18.02,8.3 18.02,11.09 L 18.02,12.35 15.47,12.35 15.47,9.83 43.52,9.83 43.52,12.35 40.98,12.35 40.98,11.09 Z M 40.98,11.09" />
              <rect id="roundedRectangle27" stroke="none" fill="rgb(29, 180, 255)" x="4" y="11" width="51" height="6" rx="3" />
            </g>
          </svg>
        </a>
      </div>
      <div>
        <h1>{{indice}}</h1>

        <input name="name" type="text" placeholder="Name" value="{{name}}" />
        <input name="quantity" type="hidden"  value="1" />
        <input name="number" type="text" placeholder="Number" value="{{number}}" />

        <select name="size">
          <option selected="selected" value="">Size</option>
          <?php foreach ($size_values as $key => $size) : ?>
            <option value="<?php echo $size->slug; ?>"><?php echo $size->name; ?></option>
          <?php endforeach; ?>
        </select>
        <a href="javascript:saveEditarJersey('jersey{{indice}}');" class="saveEditarJersey" >Save</a>
        <a href="javascript:ocultarNewJersey('jersey{{indice}}');" class="cancelEditarJersey">Cancel</a>
      </div>
    </hgroup>
  </div>
  <a href="javascript:DesbloquearYmostrarPestanha(5);" class="">Confirm</a>
</article>