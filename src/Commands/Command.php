<?php


namespace EasyWeChat\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * Class Command
 *
 * @author overtrue <i@overtrue.me>
 */
class Command extends \Symfony\Component\Console\Command\Command
{
    protected $name;
    protected $description;
    protected $help = '';
    protected $arguments = [];
    protected $options = [];

    /**
     * The console command input.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The console command output.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help)
        ;

        foreach ($this->arguments as $argument) {
            $this->addArgument(...$argument);
        }

        foreach ($this->options as $option) {
            $this->addOption(...$option);
        }
    }


    /**
     * @inheritdoc
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);
    }

    /**
     * Get the value of a command argument.
     *
     * @param string $key
     * @param null   $default
     *
     * @return string|array
     */
    public function argument($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key) ?? $default;
    }

    /**
     * Get all arguments.
     *
     * @return array
     */
    public function arguments()
    {
        return $this->input->getArguments();
    }

    /**
     * Get the value of a command option.
     *
     * @param string $key
     * @param null   $default
     *
     * @return string|array
     */
    public function option($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key) ?? $default;
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function options()
    {
        return $this->input->getOptions();
    }

    /**
     * Confirm a question with the user.
     *
     * @param string $question
     * @param bool   $default
     *
     * @return bool
     */
    public function confirm($question, $default = true)
    {
        return $this->output->confirm($question, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param string $question
     * @param string $default
     *
     * @return string
     */
    public function ask($question, $default = null)
    {
        return $this->output->ask($question, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param string $question
     * @param array  $choices
     * @param string $default
     *
     * @return string
     */
    public function anticipate($question, array $choices, $default = null)
    {
        return $this->askWithCompletion($question, $choices, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param string $question
     * @param array  $choices
     * @param string $default
     *
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);

        return $this->output->askQuestion($question);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param string $question
     * @param bool   $fallback
     *
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * Write a string as standard output.
     *
     * @param string $string
     */
    public function line($string)
    {
        $this->output->writeln($string);
    }

    /**
     * Write a string as information output.
     *
     * @param string $string
     */
    public function info($string)
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Write a string as comment output.
     *
     * @param string $string
     */
    public function comment($string)
    {
        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * Write a string as question output.
     *
     * @param string $string
     */
    public function question($string)
    {
        $this->output->writeln("<question>$string</question>");
    }

    /**
     * Write a string as error output.
     *
     * @param string $string
     */
    public function error($string)
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Aborts command execution.
     *
     * @param string $string
     */
    public function abort($string)
    {
        $this->error($string);
        exit;
    }

    /**
     * Call another console command.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     * @throws \Exception
     */
    public function call($command, array $arguments = [])
    {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;

        return $instance->run(new ArrayInput($arguments), $this->output);
    }

    /**
     * Get command line.
     *
     * @param bool $trimCommand
     *
     * @return string
     */
    public function getCommandLine($trimCommand = false)
    {
        $raw = strval($this->input);

        if (!$trimCommand) {
            return $raw;
        }

        return trim(strstr($raw, ' '));
    }

    /**
     * Call another console command silently.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     * @throws \Exception
     */
    public function callSilent($command, array $arguments = [])
    {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;

        return $instance->run(new ArrayInput($arguments), new NullOutput());
    }
}