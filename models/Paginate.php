<?php

class Paginate
{
    private $current_page;
    private $page_rows;
    private $total_page;
    private $show_nav = 5;
    private $path = '?p=';
    private $loop_start;
    private $loop_end;

    public function __construct($current_page, $total_record, $page_rows = 10)
    {
        $this->current_page = $current_page;
        $this->page_rows = $page_rows;

        $this->total_page = ceil($total_record / $page_rows);
    }

    private function showNavi()
    {
        if ($this->total_page < $this->show_nav) {
            $this->show_nav = $this->total_page;
        }

        if ($this->total_page <= 1 || $this->total_page < $this->current_page) {
            return;
        }
    }

    private function showCenter()
    {
        $show_navh = floor($this->show_nav / 2);

        $this->loop_start = $this->current_page - $show_navh;
        $this->loop_end = $this->current_page + $show_navh;
    }

    private function pageSide()
    {
        if ($this->loop_start <= 0) {
            $this->loop_start = 1;
            $this->loop_end = $this->show_nav;
        }

        if ($this->loop_end > $this->total_page) {
            $this->loop_start = $this->total_page - $this->show_nav + 1;
            $this->loop_end = $this->total_page;
        }
    }

    public function getOffset()
    {
        $start = 0;

        if ($this->current_page > 1) {
            $start = ($this->current_page * $this->page_rows) - $this->page_rows;
        }

        return $start;
    }

    public function pagenation($data)
    {
        $this->showNavi();
        $this->showCenter();
        $this->pageSide();

        if(isset($data['value'])) {
            $order = '&' . $data['name'] . '=' .$data['value'];
        } else {
            $order = '';
        }



        if ($this->current_page > 2) {
        //  echo '<li class="first"><a href="'.$this->path.'1'.$order.'">&laquo;</a></li>'."\n";
            echo '
                <li class="page-item first">
                    <a class="page-link" href="'.$this->path.'1'.$order.'" aria-label="Previous">««<span> To the top</span></a>
                </li>
            ';
        }

        if ($this->current_page > 1) {
        //  echo '<li class="prev"><a href="'.$this->path.($this->current_page - 1).''.$order.'">&lsaquo;</a></li>'."\n";
            echo '
                <li class="page-item">
                    <a class="page-link" href="'.$this->path.($this->current_page - 1).''.$order.'" aria-label="Previous">«<span> Previous</span></a>
                </li>
            ';
        }

        for ($i = $this->loop_start; $i <= $this->loop_end; ++$i) {
            if ($i > 0 && $this->total_page >= $i) {
                if ($i == $this->current_page) {
                    echo '<li class="page-item active">';
                } else {
                    echo '<li class="page-item">';
                }

                echo '<a class="page-link" href="'.$this->path.$i.''.$order.'">'.$i.'</a>';
                echo '</li>';
            }
        }

        if ($this->current_page < $this->total_page) {
        //  echo '<li class="next"><a href="'.$this->path.($this->current_page + 1).''.$order.'">&rsaquo;</a></li>'."\n";
            echo '
                <li class="page-item">
                    <a class="page-link" href="'.$this->path.($this->current_page + 1).''.$order.'" aria-label="Next">next </span>»</span></a>
                </li>
            ';
        }

        if ($this->current_page < $this->total_page - 1) {
        //  echo '<li class="last"><a href="'.$this->path.$this->total_page.''.$order.'">&raquo;</a></li>'."\n";
            echo '
                <li class="page-item last">
                    <a class="page-link" href="'.$this->path.$this->total_page.''.$order.'" aria-label="Next">To the end </span>»»</span></a>
                </li>
            ';
        }

    }
}
