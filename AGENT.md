# MoodleBox Plugin - Agent Guidelines

## Project Context

We are in a context of a Moodle plugin. This plugin's aim is to provide a web interface to manage a MoodleBox.

The current project objective is to add a new functionality to the plugin. This functionality is to allow the Rasberry Pi to send data about the  current installation to a known serveur.

## Moodle plugin constraints

The plugin must be compatible with Moodle 4.5 and later. The agent must strictly follow Moodle's coding standards and best practices. See `/doc/coding-bible.md` for details.

## Rules

- stricty follow Moodle's coding standards and best practices
- use the Moodle plugin architecture
- use the Moodle plugin guidelines
- when create a new function, method or class, create unit tests for it and run them

## Git commits
Constructing a clear and informative commit is an important aspect of the craft of creating open source code and the history of commits is a vital part of the communication between developers. Time should be spent on crafting commits appropriately and using the git tools to achieve it.

# Git commits should:

- Tell a perfect, cleaned up version of the history. As if the code was written perfectly first time.
- Include the MDL-xxxx issue number associated with the change
- Include CODE AREA when appropriate. (Code area, is just a short name for the area of Moodle that this change affects. It can be a component name if that makes sense, but does not have to be. Remember that your audience here is humans not computers, so if a shortened version of a component name is more readable and distinctive, use that instead.)
- Be formatted as:
  - an example of correct behaviour
  - MDL-xxxx CODE AREA: short summary (72 chars soft limit)
  - Blank line on line 2, followed by an unlimited length detailed explanation
  - following if necessary. This section might include the motivation for the change
  - and contrast it with the previous behaviour.

Git commits should not:

- Include changes from bugs found and fixed before integration
- Include many separate revisions to the same lines of code for a single issue
- Arbitrarily split when part of a atomic set of logical changes
- For more guidance, see Commit cheat sheet
