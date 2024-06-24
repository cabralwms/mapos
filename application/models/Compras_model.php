<?php

use Piggly\Pix\StaticPayload;

if (! defined('BASEPATH')) {
    exit('No direct script acess allowrd');
}

class Compras_model extends CI_Model
{
    /**
     * author: Dheniarley Cruz
     * email: dheniarley.ds@gmail.com
     * message: Criando uma nova feature - Compras
     */

     public function __construct()
     {
        parent::__construct();
     }

     public function get($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
     {
        $lista_clientes = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->limit(25);
                $clientes = $this->db->get('clientes')->result();

                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }

        $this->db->select($fields.', clientes.nomeCliente, clientes.idClientes');
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        $this->db->join('clientes', 'clientes.idClientes = '.$table.'.clientes_id');
        $this->db->order_by('idCompras', 'desc');

        // condicionais da pesquisa
        if ($where) {
            // condicional de status
            if (array_key_exists('status', $where)) {
                $this->db->where_in('compras.status', $where['status']);
            }

            // condicional de clientes
            if (array_key_exists('pesquisa', $where)) {
                if ($lista_clientes != null) {
                    $this->db->where_in('compras.clientes_id', $lista_clientes);
                }
            }

            // condicional data Compra
            if (array_key_exists('de', $where)) {
                $this->db->where('compras.dataCompra >=', $where['de']);
            }
            // condicional data final
            if (array_key_exists('ate', $where)) {
                $this->db->where('compras.dataCompra <=', $where['ate']);
            }
        }
        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    } 

    public function getCompras($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $lista_clientes = [];

        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->limit(25);
                $clientes = $this->db->get('clientes')->result();

                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }

        $this->db->select($fields . ',clientes.idClientes, clientes.nomeCliente, clientes.celular as celular_cliente, usuarios.nome, garantias.*');
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = compras.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = compras.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = compras.garantias_id', 'left');
        $this->db->join('produtos_compras', 'produtos_compras.compras_id = compras.idCompras', 'left');
        $this->db->join('servicos_compras', 'servicos_compras.compras_id = compras.idCompras', 'left');

        // condicionais da pesquisa

        // condicional de status
        if (array_key_exists('status', $where)) {
            $this->db->where_in('status', $where['status']);
        }

        // condicional de clientes
        if (array_key_exists('pesquisa', $where)) {
            if ($lista_clientes != null) {
                $this->db->where_in('compras.clientes_id', $lista_clientes);
            }
        }

        // condicional data inicial
        if (array_key_exists('de', $where)) {
            $this->db->where('dataInicial >=', $where['de']);
        }
        // condicional data final
        if (array_key_exists('ate', $where)) {
            $this->db->where('dataFinal <=', $where['ate']);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('compras.idCompras', 'desc');
        $this->db->group_by('compras.idCompras');

        $query = $this->db->get();

       $result = !$one ? $query->result() : $query->row();

       return $result;
     }

    public function getById($id)
    {
        $this->db->select('compras.*, clientes.*, clientes.email as emailCliente, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome');
        $this->db->from('compras');
        $this->db->join('clientes', 'clientes.idClientes = compras.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = compras.usuarios_id');
        $this->db->where('compras.idCompras', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
            if ($returnId) {
                return $this->db->insert_id();
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function isEditable($id = null)
    {
        if ($compras = $this->getById($id)) {
            if ($compras->faturado) {
                return false;
            }
        }
        return true;
    }
    
    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function getProdutos($id = null)
    {
        $this->db->select('itens_de_compras.*, produtos.*');
        $this->db->from('itens_de_compras');
        $this->db->join('produtos', 'produtos.idProdutos = itens_de_compras.produtos_id');
        $this->db->where('compras_id', $id);
        
        return $this->db->get()->result();
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('descricao', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label'=>$row['descricao'].' | Preço: R$ '.$row['precoCompra'].' | Estoque: '.$row['estoque'],'estoque'=>$row['estoque'],'id'=>$row['idProdutos'],'preco'=>$row['precoCompra']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteCliente($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('nomeCliente', $q);
        $this->db->or_like('documento', $q);
        $query = $this->db->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label'=>$row['nomeCliente'].' | Telefone: '.$row['telefone'].' | Documento: '.$row['documento'],'id'=>$row['idClientes']];
            }
            echo json_encode($row_set);
        } else {
            $row_set[] = ['label'=> 'Adicionar cliente...', 'id' => null];
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label'=>$row['nome'].' | Telefone: '.$row['telefone'],'id'=>$row['idUsuarios']];
            }
            echo json_encode($row_set);
        }
    }

    public function atualizar_estoque_produto($idProduto, $novaQuantidade) {
        // Realize a lógica para atualizar o estoque no banco de dados
        // Exemplo de código para atualizar o estoque do produto com base no $idProduto e $novaQuantidade
        // Lembre-se de adaptar essa lógica de acordo com o seu esquema de banco de dados
        // Você pode precisar ajustar os nomes das tabelas e colunas conforme necessário
        $this->db->set('quantidade', $novaQuantidade);
        $this->db->where('id', $idProduto);
        return $this->db->update('produtos');
    }

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }

        $produtos = $this->getProdutos($id);
        $valorDesconto = $this->getById($id);
        $totalProdutos = array_reduce(
            $produtos,
            function ($carry, $produto) {
                return $carry + ($produto->quantidade * $produto->preco);
            },
            0
        );
        $amount = $valorDesconto->valor_desconto != 0 ? round(floatval($valorDesconto->valor_desconto), 2) : round(floatval($totalProdutos), 2);

        if ($amount <= 0) {
            return;
        }

        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf("%s Venda %s", substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }


    
}

/* End of file Compras_model.php */
/* Location: ./application/models/Compras_model.php */