PROGRESS_FILE=/tmp/dependancy_JeeMySensors_in_progress
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}

function apt_install {
  sudo apt-get -y install "$@"
  if [ $? -ne 0 ]; then
    echo "could not install $1 - abort"
    rm ${PROGRESS_FILE}
    exit 1
  fi
}

echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
sudo apt-get update
echo 50 > ${PROGRESS_FILE}
apt_install python-serial python-requests python-pyudev
echo 100 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm ${PROGRESS_FILE}
