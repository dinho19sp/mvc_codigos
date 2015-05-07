<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 22/06/14
 * Time: 22:14
 */

final class LoggerTXT extends Logger
{
    /**
     *  Uso de TYPE
     *  Extensão que o arquivo será salvo
     */
    const TYPE = 'txt';
    /**
     *  Pega a extensão em que
     *  o arquivo deve ser salvo
     *  @access public
     *  @return void
     */
    public function getType( )
    {
        return self::TYPE;
    }
    /**
     *  Define a mensagem a ser gravada
     *  @param string $message A mensagem que
     *  será gravada no arquivo de LOG
     *  @access public
     *  @return LoggerTXT Refêrencia ao próprio objeto
     */
    public function message( $message )
    {
        $this->storage[ ] = sprintf( '[%s] Warning:  %s%s', date( 'd-M-Y H:i:s' ), $message, PHP_EOL );
        return $this;
    }

    /*$log = new LoggerTXT( 'error_log' );
    $log->message( 'Fulano tentou acessar a pagina restrita' )
    ->write( );
    // irá gravar
    // [22-Jul-2011 21:32:42] Warning:  Fulano tentou acessar a pagina restrita*/
}