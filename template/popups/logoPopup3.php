<section id="logoPopup3" class="">
  <hgroup>

    <header>
      <h1>
        Artwork
      </h1>

      <a href="javascript:ocultarPopUpGeneral();">
        X
      </a>
    </header>

    <article>
      <section id="uploadArtworkSection">
        <header>
          Upload Artwork
        </header>

        <hgroup>
          <input id="fileupload_name" type="text" disabled="disabled" placeholder="No selected"/>
          <span class="fileinput-button">Browse<input id="fileupload" type="file" name="files[]" ></span>
        </hgroup>

        <hgroup>
          <h3>I have read and understand the logo requirements.</h3>
          <input id="fileupload_requirements" type="checkbox"/>
        </hgroup>
        <!--<a href="javascript:confirmarUpload();">Upload</a>-->
        <button id="btn_fileupload" class="btn_upload">Upload</button>
      </section>

      <section>
        <header>
          or
        </header>
      </section>

      <section id="selectArtworkSection">
        <header>
          Select Artwork
        </header>

        <hgroup>
          <header>Name</header>
          <header>Size</header>
          <header>Date</header>
          <ul id="wrapper_logos_uploaded"  >
            <a data-template="" href="javascript:seleccionarLogoEnPopup3({{indice}});" data-filename ="{{filename}}" data-name_logo ="{{name}}">
              <span>{{name}}</span>
              <span>{{size}}</span>
              <span>{{date_register}}</span>
            </a>
          </ul>
        </hgroup>
        <a id="delete_logo" href="javascript:eliminarLogoEnPopup3();" class="desactivado">
          Delete
        </a>
        <a id="select_logo" href="javascript:ConfirmarSeleccionLogoEnPopup3();" class="desactivado">
          Select
        </a>
      </section>
    </article>
    <article>
      <header>
        Logo File Requeriments
      </header>

      <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque venenatis nec diam vitae pellentesque. In mi ante, egestas consequat urna sit amet, volutpat consequat felis. Aliquam consequat urna in tempus tempus. Mauris at vehicula lectus. Vestibulum eu sem eu massa commodo ultrices ut id velit.
      </p>

      <figure>
      </figure>

      <a href="">
        Download Logo Specifications
      </a>

    </article>
  </hgroup>
</section>