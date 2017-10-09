<?php
//BUSCANDO AS CLASSES
include_once "Conexao.class.php";
include_once "Funcoes.class.php";
//CRIANDO A CLASSE
class Funcionario{
	//ATRIBUTOS
	private $con;
	private $objfc;
	private $idFuncionario;
	private $nome;
	private $email;
	private $senha;
	private $dataCadastro;
	//CONSTRUTOR
	public function __construct(){
		$this->con = new Conexao();
		$this->objfc = new Funcoes();
	}
	//METODOS MAGICO
	public function __set($atributo, $valor){
		$this->$atributo = $valor;
	}
	public function __get($atributo){
		return $this->$atributo;
	}
	//METODOS
	
	public function querySeleciona($dado){
		try{
			$this->idFuncionario = $this->objfc->base64($dado, 2);
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email`, `data_cadastro` FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			if($cst->execute()){
				return $cst->fetch();
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function querySelect(){
		try{
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email`, `data_cadastro` FROM `funcionario`");
			$cst->execute();
			return $cst->fetchAll();
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryInsert($dados){
		try{
			$this->nome = $this->objfc->tratarCaracter($dados['nome'], 1);
			$this->email = $this->objfc->tratarCaracter($dados['email'], 1);
			$this->senha = sha1($dados['senha']);
			$this->dataCadastro = $this->objfc->dataAtual(2);
			$cst = $this->con->conectar()->prepare("INSERT INTO `funcionario` (`nome`, `email`, `senha`, `data_cadastro`) VALUES (:nome, :email, :senha, :data);");
			$cst->bindParam(":nome", $this->nome, PDO::PARAM_STR);
			$cst->bindParam(":email", $this->email, PDO::PARAM_STR);
			$cst->bindParam(":senha", $this->senha, PDO::PARAM_STR);
			$cst->bindParam(":data", $this->dataCadastro, PDO::PARAM_STR);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Error ao cadastrar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryUpdade($dados){
		try{
			$this->idFuncionario = $this->objfc->base64($dados['func'], 2);
			$this->nome = $dados['nome'];
			$this->email = $dados['email'];
			$cst = $this->con->conectar()->prepare("UPDATE `funcionario` SET `nome` = :nome, `email` = :email WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			$cst->bindParam(":nome", $this->nome, PDO::PARAM_STR);
			$cst->bindParam(":email", $this->email, PDO::PARAM_STR);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Error ao alterar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryDelete($dado){
		try{
			$this->idFuncionario = $this->objfc->base64($dado, 2);
			$cst = $this->con->conectar()->prepare("DELETE FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Erro ao deletar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function logaFuncionario($dados){
		$this->email = $dados['email'];
		$this->senha = sha1($dados['senha']);
		try{
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `email`, `senha` FROM `funcionario` WHERE `email` = :email AND `senha` = :senha;");
			$cst->bindParam(':email', $this->email, PDO::PARAM_STR);
			$cst->bindParam(':senha', $this->senha, PDO::PARAM_STR);
			$cst->execute();
			if($cst->rowCount() == 0){
				header('location: login/?login=error');
			}else{
				session_start();
				$rst = $cst->fetch();
				$_SESSION['logado'] = "sim";
				$_SESSION['func'] = $rst['idFuncionario'];
				header("location: login/admin");
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMassage();
		}
	}
	
	public function funcionarioLogado($dado){
		$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email` FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
		$cst->bindParam(':idFunc', $dado, PDO::PARAM_INT);
		$cst->execute();
		$rst = $cst->fetch();
		$_SESSION['nome'] = $rst['nome'];
	}
	
	public function sairFuncionario(){
		session_destroy();
		header ('location: http://localhost/login');
	}
	
}
?>
