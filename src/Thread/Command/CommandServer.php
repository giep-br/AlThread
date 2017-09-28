<?php
namespace AlThread\Thread\Command;
use AlThread\Thread\JobException\JobException;
use AlThread\Thread\SystemFacade;

/**
*  This class store and execute the commands
* @author Georgio Barbosa da Fonseca <georgio.barbosa@gmail.com>
*/
class CommandServer
{
    const NAMESPACE_STR = "\\AlThread\Thread\\Command";
    /**
    * @var Array<Command> $comand_queue;
    */
    private $command_queue;
    private $command_list = null;
    private $system = null;

    public function __construct(SystemFacade $system)
    {
        $this->system = $system;
        $this->command_list = array(
            "StopJob" => self::NAMESPACE_STR."\\CommandStopJob",
            "RestartJob" => self::NAMESPACE_STR."\\CommandRestartJob"
        );
    }

    public function storeAndExecute(AbstractCommand $cmd)
    {
        $this->command_queue[] = $cmd;
        $cmd->execute();
    }

    public function comandFromJobException(JobException $exception)
    {
        $exception_name = (new \ReflectionClass($exception))->getShortName();
        return $this->makeCommand($exception_name);
    }

    public function makeCommand($comand_name)
    {
        if(!array_key_exists($comand_name, $this->command_list)) {
            throw new \UnexpectedValueException(
                "invalid System command: $comand_name"
            );
        }
        $comand_class = $this->command_list[$comand_name];
        return (new $comand_class($this->system));
    }
}
