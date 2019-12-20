<?php

class modalCreate {

  public function modal_display ( $data ) {

    $modal = '<div class="modal fade contents-continuity" id="contents-continuity" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:30px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="text">'.$data[ 'contents_name' ].'</p>
                </div>
                <div class="modal-body">
                  <div id="player_wrap">
                      <div class="title"></div>
                      <div id="player"></div>
                  </div>
                    <ul class="btns">
                      <li class="playerBottom"><button class="playerStop" data-dismiss="modal">STOP</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';

    return $modal;
  }

}

 ?>
