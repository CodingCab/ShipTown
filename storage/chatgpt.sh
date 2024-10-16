#!/bin/bash

# Define function to create Artisan command
create_artisan_command() {
    local COMMAND_NAME=$1
    local COMMAND_CLASS=$2

    cd ../app/Console/Commands

    # Create Command Class
    cat <<EOL > $COMMAND_CLASS.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class $COMMAND_CLASS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected \$signature = 'make:bash-script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected \$description = 'Generate a bash script file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \$scriptContent = <<<'SCRIPT'
#!/bin/bash

echo "Modifying files..."

# Example of modifying a file
# sed -i 's/original/new/g' ../path/to/file

echo "Creating new files..."

# Example of creating a new file
# echo "File content" > ../path/to/newfile

SCRIPT;

        file_put_contents(storage_path('bash_script.sh'), \$scriptContent);
        chmod(storage_path('bash_script.sh'), 0755);

        \$this->info('Bash script generated successfully.');
        return 0;
    }
}
EOL

    cd ../../..

    # Register command in Kernel
    sed -i "/protected \$commands = \[/a \        App\\\\Console\\\\Commands\\\\$COMMAND_CLASS::class," ./app/Console/Kernel.php

    echo "Artisan command created and registered successfully."
}

# Variables for Artisan command
COMMAND_NAME="make:bash-script"
COMMAND_CLASS="GenerateBashScript"

create_artisan_command $COMMAND_NAME $COMMAND_CLASS

echo "Operation completed."