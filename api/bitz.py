#coding:utf-8


"""

    Path: Url path.
    Secret : Secret applied in Bit-Z.
    Params : Pass in the request parameter as a dictionary.

"""

import hashlib
import requests


def singletons(cls, *args, **kw):
    instances = {}
    def _singleton():
        if cls not in instances:
            instances[cls] = cls(*args, **kw)
        return instances[cls]
    return _singleton


@singletons
class Bit_ZAPI(object):
    def signature(self,Path,Secret,Params):
        self.url = 'https://www.bit-z.com'
        self.path = Path
        iniSign = ''
        list = []
        for key in sorted(Params.keys()):
            iniSign += key + '=' + str(Params[key]) + '&'
            signs = iniSign[:-1]
            list.append(signs)
        sign = list[-1]
        data = sign + Secret
        Params['sign'] = hashlib.md5(data.encode("utf8")).hexdigest().lower()
        reValue = requests.post(self.url+self.path, Params, timeout=30)
        return reValue.json()
