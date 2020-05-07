<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model {

  public function  __construct() {
      parent::__construct();
  }

  public function get($table, $where = [], $joins = []){
    $this->db->select('*');
    $this->db->from($table);

    if(!empty($joins)){
      foreach ($joins as $join) {
        $this->db->join($join["table"], $join["condition"], $join["type"]);
      }
    }

    if (!empty($where)){
      $this->db->where($where)->order_by('1');
    } 

    return $this->db->get()->result();
  }

  public function create($table, $data){
    if($this->db->insert($table, $data)){
        return $this->db->insert_id();
      } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
        echo json_encode(["Validation" => ['code' => $this->db->error()['code'], 'message' => $this->db->error()['message']]]);
        return null;
      }
  }

  public function update($table, $data, $where){
    return $this->db->update($table, $data, $where);
  }

  public function delete($table, $where){
    return $this->db->delete($table, $where);
  }

  public function check_login($userData){
    $query = $this->db->get_where('users', array('uu_Email' => $userData['uu_Email']));
    if ($this->db->affected_rows() > 0) {

      if($query->row('uu_Ativo') === "True"){
        $password = $query->row('uu_Senha');
        if (md5($userData['uu_Senha']) === $password) {
            $row = $query->row_array();
            unset($row["uu_Senha"]);
            return [
                'status' => "TRUE",
                'data' => $row,
                'message' => "Login realizado com sucesso!!",
                'method' => "POST"
            ];

        } else {
            return ['status' => "FALSE", 'error' => ["uu_Senha" => "Invalid Password "], 'message' => "Erro ao validar o Formulário.", 'method' => "POST"];
        }
      } else {
        return ['status' => "FALSE", 'error' => ["uu_Email" => "Usuário Desativado"], 'message' => "Erro ao validar o Formulário.", 'method' => "POST"];
      }

    } else {
        return ['status' => "FALSE", 'error' => ["uu_Email" => "Invalid Email"], 'message' => "Erro ao validar o Formulário.", 'method' => "POST"];
    }
  }

  public function forgot($email){
    $query = $this->db->get_where('users', array('uu_Email' => $email));
    if ($this->db->affected_rows() > 0) {

      $hash = md5(date('Y-m-d H:i:s'));
      $dataLimite = date_format(date_add(new DateTime("now"), date_interval_create_from_date_string('3 days')), 'Y-m-d');

      $row = $query->row_array();

      $this->db->update(
        'users', 
        ["uu_IdReset" => $hash, "uu_DataReset" => $dataLimite], 
        ["uu_Id" => $row["uu_Id"]]
      );

      return [
        'status' => "TRUE",
        'data' => ["uu_Email" => $email, "uu_Nome" => $row["uu_Nome"], "uu_IdReset" => $hash],
        'message' => "Enviamos o link de acesso para restaurar sua senha.",
        'method' => "POST"
      ];
    } else {
      return ['status' => "FALSE", 'error' => ["uu_Email" => "Invalid Email"], 'message' => "Erro ao validar o Formulário.", 'method' => "POST"];
    }
  }

  public function recover($hash, $senha){
    $row = $this->db->query("SELECT * FROM users WHERE uu_IdReset = '$hash' AND uu_DataReset >= CURDATE()")->row_array();
    if ($this->db->affected_rows() > 0) {
      $this->db->update(
        'users', 
        ["uu_Senha" => $senha, "uu_IdReset" => null, "uu_DataReset" => null], 
        ["uu_Id" => $row["uu_Id"]]
      );
      return [
        'status' => "TRUE",
        'data' => [],
        'message' => "Senha recuperada com sucesso!",
        'method' => "POST"
      ];
    } else {
      return ['status' => "FALSE", 'error' => ["uu_Email" => "Tempo limite expirado para recuperar a senha."], 'message' => "Erro ao validar o Formulário.", 'method' => "POST"];
    }
  }

  public function getPerfilMenu($UsersId, $PerfisId){
    $sql = "SELECT m.mns_Id, m.mns_Nome, m.mns_Icone, m.mns_url, m.mns_Ativo
              FROM perfismenu pm
              LEFT JOIN menus m ON m.mns_Id = pm.pm_MenusId
              LEFT JOIN perfisuser pu ON pu.pu_PerfisId = pm.pm_PerfisId
              LEFT JOIN perfis p ON p.ps_Id = pu.pu_PerfisId
             WHERE pu.pu_UsersId = {$UsersId}
               AND pu.pu_PerfisId = {$PerfisId}
               AND p.ps_Ativo = 'True'
               AND m.mns_Ativo = 'True'
             GROUP BY m.mns_Id, m.mns_Nome, m.mns_Icone, m.mns_url, m.mns_Ativo";
    $menus = $this->db->query($sql)->result();

    foreach ($menus as $key => $menu) {
      $sql = "SELECT * FROM submenus WHERE sbm_MenusId = {$menu->mns_Id} AND sbm_Ativo = 'True' AND sbm_nivel IS NULL";
      $submenus = $this->db->query($sql)->result();
      $menus[$key]->submenus = $submenus;
      foreach ($submenus as $key1 => $submenu) {
        $sql = "SELECT * FROM submenus WHERE sbm_MenusId = {$menu->mns_Id} AND sbm_Ativo = 'True' AND sbm_nivel = {$submenu->sbm_Id}";
        $nivel = $this->db->query($sql)->result();
        $menus[$key]->submenus[$key1]->sbm_nivel = $nivel;
      }
    }

    return ["status" => "TRUE", "data" => $menus, "message" => "Montagem do Menu Realizada com Sucesso!", "method" => "GET"];
  }
}