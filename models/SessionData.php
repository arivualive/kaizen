<?php

class SessionData
{

//  public static function getRegister()
//  {
//      if (isset($_SESSION['auth']['admin_id'])) {
//          $data['register_id'] = $_SESSION['auth']['admin_id'];
//          $data['user_level_id'] = 0;
//      }
//
//      if (isset($_SESSION['auth']['teacher_id'])) {
//          $data['register_id'] = $_SESSION['auth']['teacher_id'];
//          $data['user_level_id'] = 1;
//      }
//
//      if (isset($_SESSION['auth']['student_id'])) {
//          $data['register_id'] = $_SESSION['auth']['teacher_id'];
//          $data['user_level_id'] = 2;
//      }
//
//      return $data;
//  }

    public static function getRegister($register_id, $level_id)
    {
        if (isset($_SESSION['auth']['admin_id'])) {
            $data[$register_id] = $_SESSION['auth']['admin_id'];
            $data[$level_id] = 0;
        }

        if (isset($_SESSION['auth']['teacher_id'])) {
            $data[$register_id] = $_SESSION['auth']['teacher_id'];
            $data[$level_id] = 1;
        }

        if (isset($_SESSION['auth']['student_id'])) {
            $data[$register_id] = $_SESSION['auth']['teacher_id'];
            $data[$level_id] = 2;
        }

        return $data;
    }
}
