<?php
/**
 *  Classe base para a criação de arquivos de LOG
 *  Created on 2011-07-22
 *  PHP version 5.3.0 and later
 *  @author Carlos Coelho <coelhoduda@hotmail.com>
 *  @version 0.2
 */
abstract class Logger
{
    /**
     *  @var string Armazena o nome
     *  local do arquivo de LOG
     *  @access protected
     */
    protected $filename;
    /**
     *  @var string Armazena a mensagem
     *  @access protected
     */
    protected $storage;
    /**
     *  O construtor da classe Logger
     *  @param string $filename O local do arquivo de LOG
     *  @access public
     *  @return void
     */
    public function __construct( $filename = null )
    {
        $this->filename = $filename;
    }
    /**
     *  Grava a mensagem no arquivo de LOG
     *  @access public
     *  @return true Em caso de sucesso
     */
    public function write( )
    {
        $filename = sprintf( '%s.%s', $this->filename , $this->getType( ) );
        try
        {
            $dir = dirname( $filename );
            if( ! empty( $dir ) and ! is_dir( $dir ) )
            {
                throw new Exception( 'O diretório não existe.' );
            }
            if( file_exists( $filename ) and ! is_writable( $filename ) )
            {
                throw new Exception( 'O arquivo de destino não é gravável.' );
            }
            if( ( $fp = fopen( $filename , 'a' ) ) )
            {
                fwrite( $fp, ( string ) $this );
                fclose( $fp );
                return true;
            }
            else
            {
                throw new Exception( 'Não foi possível abrir/criar o arquivo para gravação.' );
            }
        }
        catch( Exception $e )
        {
            printf( 'Erro: %s', $e->getMessage( ) );
            return false;
        }
    }
    /**
     *  Converte o objeto para sua representação em string
     *  @access public
     *  @return string
     */
    public function __toString( )
    {
        return implode( null, $this->storage );
    }
    /**
     *  Define que o método message( ) deve ser implementado
     *  pelas classes filhas
     *  @param string A mensagem a ser gravada
     */
    abstract function message( $message );
    /**
     *  Define que o método getType( ) deve ser implementado
     *  pelas classes filhas
     */
    abstract function getType( );
}
