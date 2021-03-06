<?php

/**
 * MK_Exception
 *
 * Obsługa wyjątków dla JSON-a
 *
 * @category MK
 * @package    MK_Exception
 */
class MK_Exception extends Exception
{

    /**
     * Rozbudowany raport błędu dla MK_Exception i MK_Db_Exception.
     * Zapisanie zdarzenia w pliku tekstowym i wysłanie do logs.madkom.pl (dla developer:false)
     *
     * try {
     *    // code
     * } catch (MK_Db_Exception $e) {
     *    //MK_Error::setMoreTraceIgnorePath(array('Spirb->loadModule'));
     *    die($e->getExtendedMessage());
     * } catch (MK_Exception $e) {
     *    die($e->getExtendedMessage());
     * }
     *
     * @param bool $detailedLog
     *
     * @return string
     */
    public function getExtendedMessage($detailedLog = true)
    {
        $retArray = array (
            'success' => false,
            'message' => $this->getMessage()
        );

        $_file = $this->getFile();
        $_line = strval($this->getLine());
        $_trace = MK_Error::getExtendedTrace($this);
        $debugMsg = $dbError = '';

        if(MK_Db_PDO_Singleton::isInstance()) {
            $mkDb = new MK_Db_PDO();
            $mkDb->transFail();
            $dbError = $mkDb->getErrorMsg();
        }

        if($detailedLog == true) {
            if(empty($dbError)) {
                $debugMsg = MK_Error::fromException($retArray['message'], $_file, $_line, $_trace);
            } else {
                $debugMsg = MK_Error::fromDataBase($dbError, $_file, $_line, $_trace);
            }
        } else {
            $debugMsg = empty($dbError) ? $retArray['message'] : $dbError;
        }

        $retArray['debug'] = (MK_DEBUG === true) ? '<pre>' . $debugMsg . '</pre>' : '';

        if(MK::isAjaxExecution(true)) {
            return json_encode($retArray);
        }

        return $retArray[(MK_DEBUG === true) ? 'debug' : 'message'];
    }

}