#set -x  # make sure each command is printed in the terminal
PROGRESS_FILE=/tmp/jeedom/JeeMySensors/dependance
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "Lancement de l'installation/mise a jour des dependances de JeeMySensors"

function apt_install {
  sudo apt-get -y install "$@"
  if [ $? -ne 0 ]; then
    echo "could not install $1 - abort"
    rm ${PROGRESS_FILE}
    exit 1
  fi
}

echo 10 > ${PROGRESS_FILE}
sudo rm -f /var/lib/dpkg/updates/*
sudo apt-get clean

echo 30 > ${PROGRESS_FILE}
sudo apt-get update

echo 50 > ${PROGRESS_FILE}
echo "Installation des dependances"
apt_install python-serial python-requests python-pyudev

echo 100 > ${PROGRESS_FILE}
echo "Everything is successfully installed!"
rm ${PROGRESS_FILE}
