#!/bin/sh
set -o errexit  # Exit the script with error if any of the commands fail

PATH="$PHP_PATH/bin:$PATH"

install_extension ()
{
   rm -f ${PHP_PATH}/lib/php.ini

   if [ "x${EXTENSION_BRANCH}" != "x" ] || [ "x${EXTENSION_REPO}" != "x" ]; then
      CLONE_REPO=${EXTENSION_REPO:-https://github.com/mongodb/mongo-php-driver}
      CHECKOUT_BRANCH=${EXTENSION_BRANCH:-master}

      echo "Compiling driver branch ${CHECKOUT_BRANCH} from repository ${CLONE_REPO}"

      mkdir -p /tmp/compile
      rm -rf /tmp/compile/mongo-php-driver
      git clone ${CLONE_REPO} /tmp/compile/mongo-php-driver
      cd /tmp/compile/mongo-php-driver

      git checkout ${CHECKOUT_BRANCH}
      git submodule update --init
      phpize
      ./configure --enable-mongodb-developer-flags
      make all -j20 > /dev/null
      make install

      cd ${PROJECT_DIRECTORY}
   elif [ "${EXTENSION_VERSION}" != "" ]; then
      echo "Installing driver version ${EXTENSION_VERSION} from PECL"
      pecl install -f mongodb-${EXTENSION_VERSION}
   else
      echo "Installing latest driver version from PECL"
      pecl install -f mongodb
   fi

   sudo cp ${PROJECT_DIRECTORY}/.evergreen/config/php.ini ${PHP_PATH}/lib/php.ini

   php --ri mongodb
}

install_composer ()
{
  EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

  if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
  fi

  php composer-setup.php --quiet
  rm composer-setup.php
}

case "$DEPENDENCIES" in
   lowest*)
      COMPOSER_FLAGS="${COMPOSER_FLAGS} --prefer-lowest"
      ;;

   *)
      ;;
esac

install_extension

# install_composer

# php composer.phar update $COMPOSER_FLAGS
