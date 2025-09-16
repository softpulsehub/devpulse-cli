<p align="center">
<img src="/arts/devpulse-cli-logo.svg" alt="Overview DevPulse PHP" style="width:200px">
</p>

**DevPulse** is an open-source, lightweight command-line interface (CLI) designed to simplify and standardize development workflows. It provides a unified interface for managing scripts and server connections across platforms, boosting productivity and reducing complexity.

⚠️ **Note**: DevPulse is in active development. We're working toward a stable release—stay tuned!

## Key Features

-   🌍 **Cross-Platform Support**: Available via Composer; support for Windows, macOS, and Linux coming soon.
-   ⚡ **Quick Setup**: Auto-generates configuration on first run or with `devpulse init`.
-   🔄 **Smart Configuration Merging**: Combines global and local configurations, with local settings taking precedence.
-   📝 **User-Friendly JSON Configs**: Easy-to-edit, human-readable JSON files for scripts and servers.
-   🔒 **Full Data Control**: Store configurations locally with no vendor lock-in.
-   🛡️ **Lightweight & Secure**: Zero dependencies, safe for any environment, including servers.
-   🛠️ **Script Management**: Easily add, remove, edit, list, and run scripts with intuitive commands. Execute multiple scripts simultaneously, including concurrent execution.
-   🌐 **Server Management**: Easily add, remove, edit, list, and SSH into servers without manual configuration of host, port, or other SSH details.

## Installation

### Prerequisites

-   **PHP**: Version 8.2 or higher
-   **Composer**: Required for installation
-   **PATH Setup**: Ensure Composer's global bin directory is in your PATH. Add this to your `.bashrc`, `.zshrc`, or equivalent:
    ```bash
    export PATH="$PATH:$HOME/.composer/vendor/bin"
    ```

### Install DevPulse

1. Install globally via Composer:
    ```bash
    composer global require softpulselab/devpulse-cli
    ```
2. Verify installation:
    ```bash
    devpulse --version
    ```

## Getting Started

### Initial Setup

Run DevPulse for the first time to auto-generate a configuration file, or initialize manually:

```bash
devpulse init
```

`devpulse init` creates a `devpulse.json` file in the current directory. When executing any command, local settings (`./devpulse.json`) take precedence over global settings (e.g., `~/.devpulse/devpulse.json`).

### Configuration File

DevPulse uses a simple `devpulse.json` file to manage scripts and servers. Example:

```json
{
    "scripts": [
        {
            "name": "rector",
            "command": "./vendor/bin/rector",
            "description": "Refactor with Rector"
        },
        {
            "name": "pint",
            "command": "./vendor/bin/pint",
            "description": "Reformat code with Pint"
        },
        {
            "name": "refacto",
            "command": "devpulse run rector,pint",
            "description": "Refactor code with rector and reformat code with Pint"
        }
    ],
    "servers": [
        {
            "name": "production",
            "host": "example.com",
            "user": "deploy",
            "port": 22
        }
    ]
}
```

-   **Scripts**: Define reusable commands with optional descriptions.
-   **Servers**: Store SSH connection details for quick access.

## Script Management

Manage project scripts with straightforward commands:

| Command                       | Description                         | Example                                                         |
| ----------------------------- | ----------------------------------- | --------------------------------------------------------------- |
| `script:list`                 | List all scripts                    | `devpulse script:list`                                          |
| `script:add`                  | Add a new script (interactive mode) | `devpulse script:add`                                           |
| `script:add <name> <command>` | Add a script with inline options    | `devpulse script:add test "npm test" --description="Run tests"` |
| `script:update <name>`        | Update an existing script           | `devpulse script:update test --command="npm run test:ci"`       |
| `script:remove <name>`        | Remove a script                     | `devpulse script:remove test`                                   |
| `run <name>`                  | Run a script                        | `devpulse run test`                                             |
| `run <name>,<another-name>`   | Run multiple script at once         | `devpulse run test,test2`                                       |

### Running Scripts

Execute scripts with flexible options:

-   Basic execution:
    ```bash
    devpulse run test
    ```
-   Run concurrently:
    ```bash
    devpulse run test,test2 --concurrently
    ```
-   Pass custom options:
    ```bash
    devpulse run deploy --env=production --force
    ```

## Server Management

Simplify server connections with these commands:

| Command                    | Description                         | Example                                                              |
| -------------------------- | ----------------------------------- | -------------------------------------------------------------------- |
| `server:list`              | List all servers                    | `devpulse server:list`                                               |
| `server:add`               | Add a new server (interactive mode) | `devpulse server:add`                                                |
| `server:add <name> <host>` | Add a server with inline options    | `devpulse server:add production example.com --user=deploy --port=22` |
| `server:update <name>`     | Update server details               | `devpulse server:update production --port=2222`                      |
| `server:remove <name>`     | Remove a server                     | `devpulse server:remove production`                                  |
| `ssh <name>`               | SSH into a server                   | `devpulse ssh production`                                            |

## Contributing

We welcome contributions! Fork the repository, make changes, and submit a Pull Request. Check the repository for guidelines and open issues.

## License

DevPulse is licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Support

-   **Documentation**: [Official DevPulse Docs](https://github.com/softpulselab/devpulse-cli) (Available Soon)
-   **Issues**: Report bugs or request features on the [GitHub repository](https://github.com/softpulselab/devpulse-cli/issues)
-   **Community**: Join discussions on [X](https://x.com/search?q=%23DevPulseCLI&src=typed_query) by searching for `#DevPulseCLI`.
