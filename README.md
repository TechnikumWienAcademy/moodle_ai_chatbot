# Open AI Chat #

Die Fachhochschule Technikum Wien setzt neue Maßstäbe in der digitalen Lehre: Mit dem neuen Moodle-Plugin “AI-Bot” soll ab dem nächsten Studienjahr eine Technologie zur Unterstützung von Lernen und Lehren am Technikum zum Einsatz kommen. Das Plugin, von der FH Technikum Wien gemeinsam mit externen Partnern entwickelt, integriert die fortschrittliche Künstliche Intelligenz von OpenAI und ermöglicht den Studierenden eine personalisierte Lernerfahrung direkt in ihren Moodle-Kursen. 

Die FHTW suchte nach einem passgenauen Tool, das in individuellen Kursen in Moodle integriert werden konnte und mit den vorliegenden Datenschutzbestimmungen in Einklang steht. Ziel war es, den Studierenden eine verbesserte Unterstützung im Eigenstudium zu bieten. Aus diesem Bedarf heraus wurde das AI-Bot Moodle-Plugin entwickelt und wird nun seit Mai 2024 getestet und anschließend via Github für alle Moodle-User*innen weltweit zur Verfügung gestellt. Das Ziel ist es, den Einsatz von KI in der Lehre kontinuierlich zu verbessern und auf die Bedürfnisse der Studierenden einzugehen. Das Plugin bietet einen Chat mit ChatGPT, dabei werden keine Inhalte aus der Lehrveranstaltung hochgeladen.
Schlüsselmerkmale des AI-Bot Plugins:

    OpenAI-Integration: Das Herzstück des Plugins ist die Integration von OpenAI’s fortschrittlichem KI-Modell ChatGPT 4.0.
    Fragen-/Prompt-Limit: Lehrende können festlegen, wie viele Fragen oder Prompts jeder Studierende im Moodle-Kurs stellen darf, um eine faire Nutzung sicherzustellen. Z.B. Kann festgelegt werden, dass jede/r Studierende max. 10 Fragen pro Themengebiet stellen darf.
    Chat und Assistant: Studierende können per Chat interaktive Konversationen mit dem AI-Bot führen. In Zukunft soll, nach ausführlicher rechtlicher Prüfung, ein Assistant für spezifischere und aufgabenorientierte Antworten ebenfalls verfügbar sein.
    Completion Prompt: Dieses Feld ermöglicht es Lehrenden, Themenbereiche festzulegen, zu denen der Chat Antworten geben darf, um die Interaktionen zu steuern.
    Source of Truth: Lehrende können eine verlässliche Informationsquelle angeben, die die KI bei der Beantwortung von Fragen berücksichtigen soll, um Relevanz und Genauigkeit zu gewährleisten.
    Anonymisierter Log-Bericht: Der/die Lehrende kann sehen, welche Fragen gestellt wurden und welche Antworten ChatGPT geliefert hat. Dies optiminiert die Zusammenarbeit mit den Studierenden in der Präsenz durch passgenaue Vorbereitung aufgrund der Logs. 


„Das AI-Bot Moodle-Plugin markiert einen bedeutenden Schritt in Richtung einer modernen, interaktiven Lernerfahrung, die den individuellen Bedürfnissen der Studierenden gerecht wird. Durch die Integration von ChatGPT 4.0 können Studierende neben den Top-Lektor*innen auf hochwertige Lehrunterstützung im Eigenstudium zugreifen, die ihnen hilft, komplexe Konzepte besser zu verstehen und ihr eigenes Lernen zu vertiefen“, sagt Geschäftsführer Florian Eckkrammer.

„Die FH Technikum Wien ist stolz darauf, eine Vorreiterrolle in der Implementierung von innovativen Lehrtechnologien einzunehmen und die digitale Transformation des Bildungswesens voranzutreiben. Mit dem AI-Bot-Plugin wird die FHTW weiterhin ihre Position als führende Bildungseinrichtung festigen und ihren Studierenden die bestmögliche Lernerfahrung bieten“, so Geschäftsführerin Barbara Czak-Pobeheim.
Über die FH Technikum Wien

Die FH Technikum Wien ist Österreichs Fachhochschule für Technik und Digitalisierung. Seit ihrer Gründung im Jahr 1994 hat sie rund 17.000 Absolvent*innen hervorgebracht. Aktuell werden mehr als 4.500 Studierende in mehr als 30 Bachelor- und Master-Studiengängen zu Spitzenkräften für die Wirtschaft ausgebildet. Die Studiengänge werden in Tagesform oder Abendform angeboten. Das Studienangebot ist wissenschaftlich fundiert und gleichzeitig praxisnah. Neben einer qualitativ hochwertigen technischen Ausbildung wird auch großer Wert auf wirtschaftliche und persönlichkeitsbildende Fächer gelegt. Sehr gute Kontakte zu und Kooperationen mit Wirtschaft und Industrie eröffnen den Studierenden bzw. Absolvent*innen beste Karrierechancen. Sowohl in der Lehre als auch in der Forschung steht die Verzahnung von Theorie und Praxis an oberster Stelle.

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/openaichat

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2023 think modular

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
